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
use Drupal\ReferencesGenerator\Driver\Fields\Drupal7\NodeReferenceGenerator;
use Drupal\ReferencesGenerator\Driver\Fields\Drupal7\EntityReferenceGenerator;
use Drupal\ReferencesGenerator\Driver\Fields\Drupal7\TaxonomyTermReferenceGenerator;
use Drupal\ReferencesGenerator\Driver\Fields\Drupal7\FileGenerator;
use Drupal\ReferencesGenerator\Content\DefaultContent;

class ReferencesGeneratorContext implements DrupalAwareInterface {
  use NodeGeneratorContext;
  use ImageGeneratorContext;
  /**
   * Drupal context.
   *
   * @var Context
   */
  protected $drupalContext;
  /**
   * Raw Drupal context.
   *
   * @var Context
   */
  protected $rawDrupalContext;
  /**
   * When set to TRUE, referenced content will be automatically created if needed.
   */
  protected $automaticallyCreateReferencedItems = TRUE;

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

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function beforeScenario(BeforeScenarioScope $scope) {
  }

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function initialize(BeforeScenarioScope $scope) {
    // Get the environment.
    $environment = $scope->getEnvironment();

    // Get all the contexts we need.
    $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');
    $this->rawDrupalContext = $environment->getContext('Drupal\DrupalExtension\Context\RawDrupalContext');

    // Ensure drupal is bootstrapped by getting the driver.
    $this->drupalContext->getDriver('drupal');
  }

  /**
   * @AfterScenario
   *
   * @param AfterScenarioScope $scope
   */
  public function afterScenario(AfterScenarioScope $scope) {
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeNodeCreate
   */
  public function assignEntityTypeNode(EntityScope $scope) {
    // echo 'B4 Node';ob_flush();
    $entity = $scope->getEntity();
    $entity->entityType = 'node';
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeTermCreate
   */
  public function assignEntityTypeTerm(EntityScope $scope) {
    // echo 'B4 Term';ob_flush();
    $entity = $scope->getEntity();
    $entity->entityType = 'term';
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

    if (isset($this->useDefaultContent) && $this->useDefaultContent == TRUE) {
      // Fill in default values
      $default = new DefaultContent($entity->entityType);
      $bundleName = isset($entity->type) ? $entity->type : '';
      $defaults = $default->mapping($bundleName);
      foreach ($defaults as $fieldName => $value) {
        if (!isset($entity->{$fieldName})) {
          $entity->{$fieldName} = $defaults[$fieldName];
        }
      }

      $tmpEntity = clone $entity;
      $this->rawDrupalContext->parseEntityFields($entity->entityType, $tmpEntity);

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
            $generator->setDrupalContext($this->drupalContext);
            if (!$generator->referenceExists($fieldValue)) {
              // @todo createReferencedItem() should use $scope->getContext()->createNode() instead of this->drupalcontext
              $generator->createReferencedItem($field, $fieldValue);
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
   * Gets the generator class for the reference type.
   *
   * @param $field
   */
  private function getGenerator($entity, $fieldType, $fieldName) {
    $generator = NULL;
    switch ($fieldType) {
      case 'file':
      case 'image':
        $generator = new FileGenerator($entity, $fieldType, $fieldName);
        break;
      case 'node_reference':
        $generator = new NodeReferenceGenerator($entity, $fieldType, $fieldName);
        break;
      case 'entityreference':
        $generator = new EntityReferenceGenerator($entity, $fieldType, $fieldName);
        break;
      case 'taxonomy_term_reference':
        $generator = new TaxonomyTermReferenceGenerator($entity, $fieldType, $fieldName);
        break;
      case 'car_reference':
        //$fieldHandler = 'CarReferenceContext';
        break;
    }

    return $generator;
  }

  /**
   * Generates a table node from array.
   *
   * @param $table
   */
  public function getTableNode($table) {
    // Reformat array.
    $table = array_merge(
      array(
        array_keys($table)
      ),
      array(
        array_values($table)
      ));

    return new TableNode($table);
  }
}
