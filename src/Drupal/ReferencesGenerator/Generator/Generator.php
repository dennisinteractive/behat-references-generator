<?php

namespace Drupal\ReferencesGenerator\Generator;

// @todo Support for D8.
use Drupal\ReferencesGenerator\Generator\Drupal7\NodeReferenceGenerator;
use Drupal\ReferencesGenerator\Generator\Drupal7\EntityReferenceGenerator;
use Drupal\ReferencesGenerator\Generator\Drupal7\TaxonomyTermReferenceGenerator;
use Drupal\ReferencesGenerator\Generator\Drupal7\FileGenerator;

class Generator {
  public static function getGenerator($entity, $fieldType, $fieldName) {
    $generator = NULL;
    switch ($fieldType) {
      case 'file':
      case 'image':
        $generator = new FileGenerator($entity, $fieldType, $fieldName);
        break;
      case 'node_reference':
        $generator = new NodeReferenceGenerator($entity, $fieldType, $fieldName);
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
    }

    return $generator;
  }
}
