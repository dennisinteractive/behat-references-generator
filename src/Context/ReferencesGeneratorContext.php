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
   * @param $entity
   * @param $fieldType
   * @param $fieldName
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorInterface
   * @throws \Exception
   */
  protected function getReferenceGenerator($entity, $fieldType, $fieldName) {
    return $this->getGeneratorManager()->getReferenceGenerator($entity, $fieldType, $fieldName);
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
      $node->useDefaultContent = TRUE;
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
    if (isset($entity->useDefaultContent) && $entity->useDefaultContent == TRUE) {
      $bundleName = isset($entity->type) ? $entity->type : '';
      $defaultContent = new DefaultContent($entity->entityType, $this->defaultContentMapping);
      $defaults = $defaultContent->getContent($bundleName);

      foreach ($defaults as $field_name => $value) {
        if (!isset($entity->{$field_name})) {
          $entity->{$field_name} = $defaults[$field_name];
        }
      }

      $tmp_entity = clone $entity;
      $this->parseEntityFields($entity->entityType, $tmp_entity);

      // Create referenced entities.
      foreach ($tmp_entity as $field_name => $field_values) {
        if (empty($field_name)) {
          continue;
        }
        $field = $this->getGeneratorManager()->getField($entity->entityType, $field_name);
        $field_type = $field->getType();

        if (empty($field_type)) {
          continue;
        }

        if (!is_array($field_values)) {
          $field_values = array($field_values);
        }

        foreach ($field_values as $key => $field_value) {
          if ($generator = $this->getReferenceGenerator($entity, $field_type, $field_name)) {
            $generator->setDrupalContext($this);
            if (!$generator->referenceExists($field_value)) {
              $generator->create($field, $field_value);
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
    $table = TableNode::fromList(array('',''));
    $this->createNodes($nodeType, $table);
  }

  /**
   * @Given a default :type content:
   */
  public function DefaultContentWithOverrides($type, TableNode $table) {
    $this->createNodes($type, $table);
  }

  /**
   * @Given I am viewing a default :type content:
   */
  public function viewDefaultContentWithOverrides($type, TableNode $table) {
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
      $image = ImageGenerator::createImage($defaultImage);
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
