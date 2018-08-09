<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalDriverManager;

class GeneratorManager {
  /**
   * @var DrupalDriverManager
   */
  protected $drupal;

  /**
   * {@inheritdoc}
   */
  public function __construct(DrupalDriverManager $drupal) {
    $this->drupal = $drupal;
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
  public function getReferenceGenerator($entity, $field_type, $field_name) {
    $core = $this->drupal->getDriver()->getDrupalVersion();
    $mapping = array(
      'file' => 'File',
      'image' => 'File',
      'node_reference' => 'Node',
      'entityreference' => 'Entity',
      'entity_reference' => 'Entity',
      'taxonomy_term_reference' => 'TaxonomyTerm',
      'car_reference' => 'Car',
    );

    if (isset($mapping[$field_type])) {
      $type = $mapping[$field_type];
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal%s\Reference\%s', $core, $type);
      if (class_exists($class_name)) {
        return new $class_name($entity, $entity->entityType, $field_name);
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
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal%s\Entity\%s', $core, $mapping[$type]);
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
  public function getField($entity_type, $field_name) {
    $core = $this->drupal->getDriver()->getDrupalVersion();
    $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal%s\Field\Field', $core);
    if (class_exists($class_name)) {
      return new $class_name($entity_type, $field_name);
    }
    else {
      throw new \Exception("Cannot find $class_name class");
    }
  }

}
