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
use DennisDigital\Behat\DefaultContent\Content\DefaultContent;
use DennisDigital\Behat\DefaultContent\Generator\EntityGenerator;
use DennisDigital\Behat\DefaultContent\Generator\ImageGenerator;

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
   * @inheritdoc
   */
  public function __construct()
  {

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
    //print_r($entity); ob_flush();
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
      $defaultContent = New DefaultContent($entity->entityType);
      $defaults = $defaultContent->getContent($bundleName);

      foreach ($defaults as $fieldName => $value) {
        if (!isset($entity->{$fieldName})) {
          $entity->{$fieldName} = $defaults[$fieldName];
        }
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

    // print_r($entity); ob_flush();
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


}
