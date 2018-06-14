<?php

namespace Drupal\ReferencesGenerator\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\HookDispatcher;
use Drupal\DrupalDriverManager;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\DrupalExtension\Context\DrupalAwareInterface;
use Drupal\DrupalUserManagerInterface;
use Drupal\ReferencesGenerator\Content\DefaultContent;
use Drupal\ReferencesGenerator\Generator\EntityGenerator;
use Drupal\ReferencesGenerator\Generator\ImageGenerator;
use Drupal\ReferencesGenerator\Content\FieldAliases;

class ReferencesGeneratorContext implements DrupalAwareInterface {

  /**
   * Drupal context.
   */
  protected $drupalContext;

  /**
   * Stores files created to be deleted after testing.
   */
  private $files;

  /**
   * When set to TRUE, referenced content will be automatically created if needed.
   */
  protected $automaticallyCreateReferencedItems = TRUE;

  /**
   * Stores the field mapping.
   */
  protected $fieldMapping;

  /**
   * Stores the default content mapping.
   */
  protected $defaultContentMapping;

  /**
   * @inheritdoc
   */
  public function __construct($fieldMapping = array(), $defaultContentMapping = array())
  {
    $this->fieldMapping = reset($fieldMapping);
    $this->defaultContentMapping = reset($defaultContentMapping);
  }

  /**
   * @inheritDoc
   */
  public function setDrupal(DrupalDriverManager $drupal) {
    $this->drupal = $drupal;
  }

  /**
   * @inheritDoc
   */
  public function setDispatcher(HookDispatcher $dispatcher) {
  }

  /**
   * @inheritDoc
   */
  public function getDrupal() {
    return $this->drupal;
  }

  /**
   * @var DrupalDriverManager
   */
  private $drupal;

  /**
   * @inheritDoc
   */
  public function setDrupalParameters(array $parameters) {
  }

  /**
   * @inheritdoc
   */
  public function setUserManager(DrupalUserManagerInterface $userManager) {
  }

  /**
   * @inheritdoc
   */
  public function getUserManager() {
  }

  //-----------------------------------------------------//
  //                   Event hooks                       //
  //-----------------------------------------------------//

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function initialize(BeforeScenarioScope $scope) {
    // Get the environment.
    $environment = $scope->getEnvironment();

    // @todo find a way to get rid of this.
    $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');

    // Ensure drupal is bootstrapped by getting the driver.
    $this->drupalContext->getDriver('drupal');
  }

  /**
   * Deletes images created.
   *
   * @AfterScenario
   */
  public function deleteImages(AfterScenarioScope $scope) {
    if (empty($this->files)) {
      return;
    }

    foreach ($this->files as $file) {
      file_delete($file, TRUE);
    }
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeNodeCreate
   */
  public function assignEntityTypeNode(EntityScope $scope) {
    $entity = $scope->getEntity();
    $entity->entityType = 'node';
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeTermCreate
   */
  public function assignEntityTypeTerm(EntityScope $scope) {
    $entity = $scope->getEntity();
    $entity->entityType = 'term';
  }

  /**
   * Transforms fields with human readable names into their respective
   * machine names.
   * We loop all the values and find matches against the mapping table.
   * Ideally we should be able to replace only the first line of the table, that
   * contains the field names. The problem is that we don't have a way to detect
   * if the table is vertical or horizontal. This could lead to actual values
   * being changed if they match a field name from the mapping.
   * @todo Detect if the table is vertical or horizontal.
   *
   * @Transform table:*
   */
  public function humanFieldNames(TableNode $table) {
    $fieldAliases = New FieldAliases($this->fieldMapping);
    $aliases = $fieldAliases->getAliases();
    $table = $table->getTable();
    foreach ($table as $rowkey => $row) {
      foreach ($row as $colkey => $value) {
        $value = $table[$rowkey][$colkey];
        if (isset($aliases[$value])) {
          $table[$rowkey][$colkey] = $aliases[$value];
        }
      }
    }
    //var_dump($table);ob_flush();

    return new TableNode($table);
  }

  /**
   * Fills in default fields for known entities provided by getDefaultNode()
   * Creates referenced content if needed.
   *
   * @beforeNodeCreate
   * @beforeTermCreate
   */
  public function createNonexistingReferences(EntityScope $scope) {
    $entity = $scope->getEntity();
    if (!isset($entity->entityType)) {
      return;
    }

    // @todo move default content to a separate function.
    if (isset($this->useDefaultContent) && $this->useDefaultContent == TRUE) {
      $bundleName = isset($entity->type) ? $entity->type : '';
      $defaultContent = New DefaultContent($entity->entityType, $this->defaultContentMapping);
      $defaults = $defaultContent->getContent($bundleName);

      foreach ($defaults as $fieldName => $value) {
        if (!isset($entity->{$fieldName})) {
          $entity->{$fieldName} = $defaults[$fieldName];
        }
      }

      $tmpEntity = clone $entity;
      $this->drupalContext->parseEntityFields($entity->entityType, $tmpEntity);

      // Create referenced entities.
      foreach ($tmpEntity as $fieldName => $fieldValues) {

        $field = field_read_field($fieldName);
        if (empty($field)) {
          // Field doesn't exist.
          continue;
        }
        $fieldType = $field['type'];

        if (!is_array($fieldValues)) {
          $fieldValues = array($fieldValues);
        }

        foreach ($fieldValues as $key => $fieldValue) {
          if ($generator = EntityGenerator::getGenerator($entity, $fieldType, $fieldName)) {
            $generator->setDrupalContext($this->drupalContext);
            if (!$generator->referenceExists($fieldValue)) {
              // @todo create() should use $scope->getContext()->createNode() instead of this->drupalcontext
              $generator->create($field, $fieldValue);
            }
          }
        }
      }

      // If pathauto is enabled, set the path.
      //if (module_exists('pathauto') && isset($entity->alias)) {
      if (isset($entity->alias)) {
        // @todo there is a bug here, it sets the same path to all terms.
        $entity->path = array(
          'alias' => $entity->alias,
          'pathauto' => 0
        );
        unset($entity->alias);
      }

//      @todo do something about this
//      // Temporary fix to populate the default value of published date. This should be populated using some hook.
//      if (!isset($entity->field_published_date)) {
//        $entity->field_published_date = array(
//          'und' => array(
//            '0' => array(
//              'value' => gmDate('Y-m-d H:i:s'),
//              'timezone' => 'UTC',
//              'timezone_db' => 'UTC',
//              'date_type' => 'datetime',
//            )
//          )
//        );
//      }
      // print_r($entity); ob_flush();
    }
  }

  /**
   * Force memcache flush.
   * This is a trick to fix a bug with memcache extension.
   *
   * @afterNodeCreate
   * @afterTermCreate
   */
  public function memcacheFlush(EntityScope $scope) {
    foreach (['cache_path_alias', 'cache_path_source'] as $bin) {
      cache_get('', $bin);
    }
  }

  //-----------------------------------------------------//
  //                  Step definitions                   //
  //-----------------------------------------------------//

  /**
   * @Given a default :type content
   */
  public function DefaultContent($nodeType) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $table = TableNode::fromList(array('',''));
    $this->drupalContext->createNodes($nodeType, $table);
  }

  /**
   * @Given a default :type content:
   */
  public function DefaultContentWithOverrides($type, TableNode $table) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->drupalContext->createNodes($type, $table);
  }

  /**
   * @Given I am viewing a default :type content:
   */
  public function viewDefaultContentWithOverrides($type, TableNode $table) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->drupalContext->assertViewingNode($type, $table);
  }

  /**
   * @Given I have an image
   */
  public function defaultImage() {
    $default = new DefaultContent('image', $this->defaultContentMapping);
    $defaultImage = $default->getContent();
    $image = ImageGenerator::createImage($defaultImage);
    $this->files[] = $image;
  }

  /**
   * Creates an image, allowing fields to be overriden using a table.
   *
   * @Given I have an image:
   */
  public function defaultImageWithOverrides(TableNode $overridesTable) {
    $default = new DefaultContent('image', $this->defaultContentMapping);
    $defaultImage = $default->getContent();

    foreach ($overridesTable as $overrides) {
      foreach ($overrides as $item => $value) {
        $defaultImage[$item] = $value;
      }
      $image = ImageGenerator::createImage($defaultImage);
      $this->files[] = $image;
    }
  }

  /**
   * @Then the file :image should be available
   */
  public function theFileShouldBeAvailable($image) {
    $path = file_create_url('public://' . $image);
    $this->drupalContext->getSession()->visit($path);
    if ($this->drupalContext->getSession()->getStatusCode() !== 200) {
      throw new \Exception(sprintf('Could not find image on %s', $path));
    };
  }

}
