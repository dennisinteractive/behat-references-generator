<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager;
use Drupal\DrupalDriverManager;

class GeneratorManager {
  /**
   * @var DrupalDriverManager
   */
  protected $drupal;

  /**
   * @var DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Reference\GeneratorInterface[]
   */
  protected $generators = [];

  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent
   */
  protected $defaultContent;

  /**
   * @inheritdoc
   */
  public function __construct(DrupalDriverManager $drupal, DefaultContent $default_content) {
    $this->drupal = $drupal;
    $this->entityManager = new EntityManager($this);
    $this->defaultContent = $default_content;
  }

  /**
   * @return \Drupal\DrupalDriverManager
   */
  public function getDrupal() {
    return $this->drupal;
  }

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent
   */
  public function getDefaultContent() {
    return $this->defaultContent;
  }

  /**
   * Gets the generator class.
   *
   * @param $entity
   * @param $fieldType
   * @param $fieldName
   *
   * @return GeneratorInterface
   * @throws \Exception
   */
  public function getReferenceGenerator($entity, $field_name) {
    if (!$field_handler = $this->getFieldHandler($entity, $entity->entityType, $field_name)) {
      return FALSE;
    }

    $field_type = $field_handler->getType();
    if (empty($field_type)) {
      return FALSE;
    }

    $core = $this->getDrupal()->getDriver()->getDrupalVersion();
    $mapping = array(
      'file' => 'FileReference',
      'image' => 'FileReference',
      'node_reference' => 'NodeReference',
      'taxonomy_term_reference' => 'TaxonomyTermReference',
      'car_reference' => 'CarReference',
      'entityreference' => 'EntityReference',
      'entity_reference' => 'EntityReference',
    );

    if (isset($mapping[$field_type])) {
      $type = $mapping[$field_type];
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal%s\%s', $core, $type);
      if (class_exists($class_name)) {
        $generator = new $class_name($entity, $entity->entityType, $field_name, $this);
        $this->generators[] = $generator;
        return $generator;
      }
      else {
        throw new \Exception("Cannot find $class_name class");
      }
    }
  }

  /**
   * Gets the entity manager.
   *
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager
   */
  public function getEntityManager() {
    return $this->entityManager;
  }

  /**
   * Gets the field.
   *
   * @param $entity_type
   * @param $field_name
   * @return mixed
   * @throws \Exception
   */
  public function getFieldHandler($entity, $entity_type, $field_name) {
    $entity_manager = \Drupal::entityManager();
    $fields = $entity_manager->getFieldStorageDefinitions($entity_type);
    if (empty($fields[$field_name])) {
      return FALSE;
    }

    $core = $this->getDrupal()->getDriver()->getDrupalVersion();
    $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\Drupal%s\FieldHandler', $core);
    if (class_exists($class_name)) {
      return new $class_name($entity, $entity_type, $field_name);
    }
    else {
      throw new \Exception("Cannot find $class_name class");
    }
  }

  /**
   * Cleanup generators.
   */
  public function cleanup() {
    $this->getEntityManager()->cleanup();
  }
}
