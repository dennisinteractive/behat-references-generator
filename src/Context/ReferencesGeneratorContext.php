<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\ImageGenerator;

class ReferencesGeneratorContext extends RawDrupalContext {
  /**
   * Stores files created to be deleted after testing.
   */
  private $files;

  /**
   * When set to TRUE, referenced content will be automatically created if needed.
   */
  protected $automaticallyCreateReferencedItems = TRUE;

  /**
   * Stores the default content mapping.
   */
  protected $defaultContentMapping;

  /**
   * @var GeneratorManager
   */
  protected $generatorManager;

  /**
   * @inheritdoc
   */
  public function __construct($parameters = array()) {
    $this->defaultContentMapping = $parameters['default_content'];
  }

  /**
   * Get Generator
   *
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager
   */
  protected function getGeneratorManager() {
    if (!isset($this->generatorManager)) {
      $this->generatorManager = new GeneratorManager($this->getDrupal());
    }
    return $this->generatorManager;
  }

  /**
   * Get Generator
   *
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorInterface
   * @throws \Exception
   */
  protected function getGenerator() {
    return $this->getGeneratorManager->getGenerator($entity, $fieldType, $fieldName);
  }

  /**
   * Create an entity.
   *
   * @param $type
   * @param $data
   * @throws \Exception
   */
  protected function createEntity($type, $data) {
    $this->getGeneratorManager()->getEntity($type, $data)->save();
  }

  /**
   * Creates content of a given type provided in the form:
   * | title    | author     | status | created           |
   * | My title | Joe Editor | 1      | 2014-10-17 8:00am |
   * | ...      | ...        | ...    | ...               |
   */
  protected function createNodes($type, TableNode $nodesTable) {
    foreach ($nodesTable->getHash() as $nodeHash) {
      $node = (object) $nodeHash;
      $node->type = $type;
      $this->nodeCreate($node);
    }
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
    $this->getDrupal()->getDriver('drupal');
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
    $this->setEntityPath($entity);
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeTermCreate
   */
  public function assignEntityTypeTerm(EntityScope $scope) {
    $entity = $scope->getEntity();
    $entity->entityType = 'term';
    $this->setEntityPath($entity);
  }

  /**
   * Sets the entity path.
   *
   * @param $entity
   */
  public function setEntityPath($entity) {
    if (isset($entity->alias)) {
      // @todo there is a bug here, it sets the same path to all terms.
      $entity->path = array(
        'alias' => $entity->alias,
        'pathauto' => 0
      );
      unset($entity->alias);
    }
    else {
      $entity->path['alias'] = '';
    }
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
      $this->parseEntityFields($entity->entityType, $tmpEntity);

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
          if ($generator = $this->getGenerator($entity, $fieldType, $fieldName)) {
            $generator->setDrupalContext($this);
            if (!$generator->referenceExists($fieldValue)) {
              // @todo create() should use $scope->getContext()->createNode() instead of this->drupalcontext
              $generator->create($field, $fieldValue);
            }
          }
        }
      }

//      // If pathauto is enabled, set the path.
//      //if (module_exists('pathauto') && isset($entity->alias)) {
//      if (isset($entity->alias)) {
//        // @todo there is a bug here, it sets the same path to all terms.
//        $entity->path = array(
//          'alias' => $entity->alias,
//          'pathauto' => 0
//        );
//        unset($entity->alias);
//      }

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
    $this->createNodes($nodeType, $table);
  }

  /**
   * @Given a default :type content:
   */
  public function DefaultContentWithOverrides($type, TableNode $table) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->createNodes($type, $table);
  }

  /**
   * @Given I am viewing a default :type content:
   */
  public function viewDefaultContentWithOverrides($type, TableNode $table) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->assertViewingNode($type, $table);
  }

  /**
   * @Given a default image
   */
  public function defaultImage() {
    $default = new DefaultContent('image', $this->defaultContentMapping);
    $defaultImage = $default->getContent();
    $image = $this->createEntity('image', $defaultImage);
    $this->files[] = $image;
  }

  /**
   * Creates an image, allowing fields to be overriden using a table.
   *
   * @Given a default image:
   */
  public function defaultImageWithOverrides(TableNode $overridesTable) {
    $default = new DefaultContent('image', $this->defaultContentMapping);
    $defaultImage = $default->getContent();

    foreach ($overridesTable as $overrides) {
      foreach ($overrides as $item => $value) {
        $defaultImage[$item] = $value;
      }
      $image = $this->createEntity('image', $defaultImage);
      $this->files[] = $image;
    }
  }

  /**
   * @Then the file :image should be available
   */
  public function theFileShouldBeAvailable($image) {
    $path = file_create_url('public://' . $image);
    $this->getSession()->visit($path);
    if ($this->getSession()->getStatusCode() !== 200) {
      throw new \Exception(sprintf('Could not find image on %s', $path));
    };
  }

}
