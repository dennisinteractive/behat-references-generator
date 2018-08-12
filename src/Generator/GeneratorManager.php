<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

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
   * {@inheritdoc}
   */
  public function __construct(DrupalDriverManager $drupal) {
    $this->drupal = $drupal;
  }

  /**
   * @return \Drupal\DrupalDriverManager
   */
  public function getDrupal() {
    return $this->drupal;
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

    $core = $this->drupal->getDriver()->getDrupalVersion();
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
   * Gets the entity class.
   *
   * @param $type
   * @param $data
   * @return mixed
   * @throws \Exception
   */
  public function getEntity($type, $data) {
    $core = $this->drupal->getDriver()->getDrupalVersion();

    $mapping = array(
      'image' => 'Image',
    );

    if (isset($mapping[$type])) {
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\Drupal%s\%s', $core, $mapping[$type]);
      if (class_exists($class_name)) {
        return new $class_name($data);
      }
      else {
        throw new \Exception("Cannot find $class_name class");
      }
    }
    throw new \Exception("Cannot find entity generator class for " . $type);
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

    $core = $this->drupal->getDriver()->getDrupalVersion();
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
    foreach ($this->generators as $generator) {
      $generator->cleanup();
    }
  }
}
