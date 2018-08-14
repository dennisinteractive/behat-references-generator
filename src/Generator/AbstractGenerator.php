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
   * @var DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\FieldHandlerInterface;
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
}
