<?php

namespace Drupal\ReferencesGenerator\Generator;

class Generator {

  public static function getGenerator($entity, $fieldType, $fieldName) {
    $core = 'Drupal7'; //@todo detect core.
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
