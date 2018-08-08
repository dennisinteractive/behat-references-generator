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
  public function getReferenceGenerator($entity, $fieldType, $fieldName) {
    $core = 'Drupal' . $this->drupal->getDriver()->getDrupalVersion();

    $mapping = array(
      'file' => 'File',
      'image' => 'File',
      'node_reference' => 'Node',
      'entityreference' => 'Entity',
      'taxonomy_term_reference' => 'TaxonomyTerm',
      'car_reference' => 'Car',
    );

    if (isset($mapping[$fieldType])) {
      $type = $mapping[$fieldType];
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\%s\Reference\%s', $core, $type);
      if (class_exists($class_name)) {
        return new $class_name($entity, $fieldType, $fieldName);
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
    $core = 'Drupal' . $this->drupal->getDriver()->getDrupalVersion();

    $mapping = array(
      'image' => 'Image',
    );

    if (isset($mapping[$type])) {
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\%s\Entity\%s', $core, $mapping[$type]);
      if (class_exists($class_name)) {
        return new $class_name($data);
      }
      else {
        throw new \Exception("Cannot find $class_name class");
      }
    }
    throw new \Exception("Cannot find entity generator class for " . $type);
  }
}
