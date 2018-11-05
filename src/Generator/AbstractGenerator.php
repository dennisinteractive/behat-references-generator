<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalDriverManager;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager;

abstract class AbstractGenerator implements GeneratorInterface {
  /**
   * Generator manager.
   *
   * @var GeneratorManager
   */
  private $generatorManager;

  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\FieldHandlerInterface
   */
  private $fieldHandler;

  /**
   * @var DrupalDriverManager
   */
  private $drupal;

  /**
   * {@inheritdoc}
   */
  public function __construct(\stdClass $entity, $entity_type, $field_name, GeneratorManager $generatorManager) {
    $this->generatorManager = $generatorManager;
    $this->drupal = $this->generatorManager->getDrupal();
    $this->fieldHandler = $this->generatorManager->getFieldHandler($entity, $entity_type, $field_name);
  }

  /**
   * @inheritdoc
   */
  public function getFieldHandler() {
    return $this->fieldHandler;
  }

  /**
   * @inheritdoc
   */
  public function getGeneratorManager() {
    return $this->generatorManager;
  }

  /**
   * @inheritdoc
   */
  public function getEntityManager() {
    return $this->getGeneratorManager()->getEntityManager();
  }

  /**
   * @return DrupalDriverManager
   */
  public function getDrupal() {
    return $this->drupal;
  }

  /**
   * @inheritdoc
   */
  public function getEntityTypeId() {
    return $this->getFieldHandler()->getFieldInfo()->getSetting('target_type');
  }

  /**
   * @inheritdoc
   */
  public function getTargetBundle() {
    $target_bundle = NULL;
    if ($target_bundles = $this->getTargetBundles()) {
      $target_bundle = reset($target_bundles);
    }
    return $target_bundle;
  }

  /**
   * @inheritdoc
   */
  public function getTargetBundles() {
    $settings = $this->getFieldHandler()->getFieldConfig()->getSettings();
    if (!empty($settings['handler_settings']['target_bundles'])) {
      return $settings['handler_settings']['target_bundles'];
    }
  }

  /**
   * @inheritdoc
   */
  public function getTargetBundleKey() {
    $entity_definition = \Drupal::entityManager()->getDefinition($this->getEntityTypeId());
    // Determine target bundle restrictions.
    $target_bundle_key = NULL;
    if ($target_bundles = $this->getTargetBundles()) {
      $target_bundle_key = $entity_definition->getKey('bundle');
    }
    return $target_bundle_key;
  }
}
