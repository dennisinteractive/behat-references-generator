<?php

namespace Drupal\EntityReferenceGenerator\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\HookDispatcher;
use Drupal\DrupalDriverManager;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\DrupalExtension\Context\DrupalAwareInterface;
use Drupal\DrupalUserManagerInterface;
use Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7\NodeReferenceGenerator;
use Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7\EntityReferenceGenerator;
use Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7\TaxonomyTermReferenceGenerator;

class EntityReferenceGeneratorContext implements DrupalAwareInterface {

  /**
   * Drupal context.
   * @var Context
   */
  protected $drupalContext;

  /**
   * Raw Drupal context.
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
    echo 'B4 Node';ob_flush();
    $entity = $scope->getEntity();
    $entity->entityType = 'node';
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeTermCreate
   */
  public function assignEntityTypeTerm(EntityScope $scope) {
    echo 'B4 Term';ob_flush();
    $entity = $scope->getEntity();
    $entity->entityType = 'term';
  }

  /**
   * Fills in default fields for known entities provided by getDefaultNode()
   * Creates referenced content if needed.
   *
   * @todo this code works with terms too, so should not be in this file.
   * @beforeNodeCreate
   * @beforeTermCreate
   */
  public function createNonexistingReferences(EntityScope $scope) {
    $entity = $scope->getEntity();
    if (!isset($entity->entityType)) {
      return;
    }
    echo 'Now create '. $entity->entityType; ob_flush();
//
//    if ($entity->entityType != 'node') {
//      var_dump('Entity type not supported: ' . $entity->entityType);
//      ob_flush();
//      return;
//    }
    if (isset($this->useDefaultContent) && $this->useDefaultContent == TRUE) {
//      print_r($entity);
//      ob_flush();
//      if (!isset($entity->type)) {
//        var_dump('Bundle not supported: ' . $entity->type);
//        ob_flush();
//
//        return;
//      }
      // Fill in default values
      //$bundleName = $entity->type;
      //$defaults = $this->getDefaultNode($entity->entityType, $bundleName);
      echo 'Default content for ' . $entity->entityType . PHP_EOL; ob_flush();
      $defaults = $this->getDefaultEntityValues($entity);
      print_r($defaults);ob_flush();
      //if (!is_array($defaults)) return;//@todo remove
      foreach ($defaults as $fieldName => $value) {
        if (!isset($entity->{$fieldName})) {
          $entity->{$fieldName} = $defaults[$fieldName];
        }
      }
      // Create referenced entities.
      $tmpEntity = clone $entity;
      //var_dump($tmpEntity);
      $this->rawDrupalContext->parseEntityFields($entity->entityType, $tmpEntity);
      var_dump($tmpEntity);

      foreach ($tmpEntity as $fieldName => $fieldValues) {
        if (!is_array($fieldValues)) {
          $fieldValues = array($fieldValues);
        }
        $field = field_read_field($fieldName);
        $fieldType = $field['type'];
        echo 'Field Name:' . $fieldName . '(' . $fieldType . ')';
        if (empty($field)) {
          // Field doesn't exist.
          continue;
        }
        foreach ($fieldValues as $key => $fieldValue) {
          echo ' Value:' . $fieldValue . PHP_EOL;
          ob_flush();
          // @todo this switch should be on a function
//            echo 'Looking at Field: ' . $fieldName . PHP_EOL;
//            echo 'Field ref type: ' . $fieldType . PHP_EOL;
//            echo 'Field value: ' . $fieldValue . PHP_EOL;
          $generator = NULL;
          echo 'FT ' . $fieldType . PHP_EOL;
          switch ($fieldType) {
            case 'list_boolean':
            case 'list_text':
            case 'text_with_summary':
            case 'text':
            case 'text_long':
            case 'datetime':
            case 'asin':
              // Don't do anything.
              break;
            case 'file':
            case 'image':
              //$fieldHandler = 'FileContext';
              break;
            case 'node_reference':
              //$generator = new NodeReferenceGenerator();
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
            default:
              print ("Field type " . $fieldType . ' not supported for field ' . $fieldName . PHP_EOL);
          }

          if (isset($generator)) {
            $generator->setDrupalContext($this->drupalContext);
            echo 'GENERATOR';ob_flush();
//print_r($generator->expand(array($fieldValue)));ob_flush();exit;
            if (!$generator->referenceExists($fieldValue)) {
              echo 'Creating: ' . $fieldName . ' ' . $fieldValue . PHP_EOL; ob_flush();
              // @todo createReferencedItem() should use $scope->getContext()->createNode() instead of this->drupalcontext
              $generator->createReferencedItem($field, $fieldValue);
            }
          }
        }
        ob_flush();
        //}
//        echo PHP_EOL;
//        ob_flush();
      }
      echo 'Now set the path';ob_flush();
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
//
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
      // @todo for some reason the entity that is altered is not being passed back to nodeCreate()
      print_r($entity);
      ob_flush();
    }
  }

  /**
   * Returns default content.
   *
   * @param $entity
   *
   * @return array
   */
  private function getDefaultEntityValues($entity) {
    switch ($entity->entityType) {
      case 'node':
        return $this->getDefaultNode($entity->type);
        break;
      case 'term':
        return $this->getDefaultTerm();
//        $defaultTerm = new DefaultTaxononyTerm();
//        $vocabName = taxonomy_vocabulary_load($entity->vid);
//        return $defaultTerm->getDefaultContent($vocabName);
        break;
    }
  }

  /**
   * Provides the default field values for nodes.
   *
   * @todo use yml files
   */
  private function getDefaultNode($bundleName) {
    switch ($bundleName) {
      case 'author':
        return array(
          'title' => 'BDD Default author',
          'body' => 'BDD Author Body',
          'field_author_first_name' => 'BDD author name',
          'field_author_last_name' => 'BDD author surname',
          'status' => 1,
        );
        break;
      case 'article':
      case 'review':
      case 'gallery_adv':
      default:
        $content = array(
          'title' => sprintf('BDD Default %s content test', $bundleName),
          'body' => 'BDD Body',
          'field_sponsored' => '0',
          'field_short_teaser' => 'BDD Short teaser',
          'field_article_type' => 'BDD Article type',
          'field_main_purpose' => 'BDD content purpose',
          'field_category_primary' => 'BDD Category',
          'field_author' => 'BDD Author1, BDD Author2',
          'field_gallery_files' => 'gal_image_1.jpg, gal_image_2.jpg',
          'field_tags' => 'BDD Tag1, BDD Tag2, BDD Tag3',
          'field_primary_image' => 'bddtest.jpg',
          'alias' => sprintf('bdd-default-%s-content-test', $bundleName),
          'status' => 1,
        );
        $content['field_short_title'] = $content['title'];
        return $content;
    }
  }

  /**
   * Provides the default field values for terms.
   *
   * @todo use yml files
   */
  private function getDefaultTerm() {
    return array(
      'name' => 'BDD Term',
      'alias' => 'bdd-term',
      //'path' => array('alias' => 'bdd-term', 'pathauto' => 0),
    );
  }

  /**
   * @Given I am viewing a default :type content:
   */
  public function viewDefaultContent($type, TableNode $fields) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->drupalContext->assertViewingNode($type, $fields);
  }


}
