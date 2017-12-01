<?php

namespace Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\AbstractHandler;

class EntityGenerator extends AbstractHandler implements GeneratorInterface {

  public $drupalContext;

  /**
   * {@inheritdoc}
   */
  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  /**
   * {@inheritdoc}
   */
  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {

  }

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {

  }

  /**
   * Gets the generator class.
   *
   * @param $entity
   * @param $fieldType
   * @param $fieldName
   *
   * @return mixed
   * @throws \Exception
   */
  public static function getGenerator($entity, $fieldType, $fieldName) {
    $core = 'Drupal7'; //@todo detect Drupal core version.
    $mapping = array(
      'file' => 'File',
      'image' => 'File',
      'node_reference' => 'NodeReference',
      'entityreference' => 'EntityReference',
      'taxonomy_term_reference' => 'TaxonomyTermReference',
      'car_reference' => 'CarReference',
    );

    if (isset($mapping[$fieldType])) {
      $type = $mapping[$fieldType];
      $class_name = sprintf('\Drupal\ReferencesGenerator\Generator\%s\%sGenerator', $core, $type);
      if (class_exists($class_name)) {
        return new $class_name($entity, $fieldType, $fieldName);
      }
      else {
        throw new \Exception("Cannot find $class_name class");
      }
    }
  }
}
