<?php

namespace Drupal\ReferencesGenerator\Generator\Drupal7;

//use Drupal\DrupalExtension\Context\DrupalContext;
//use Drupal\Driver\Fields\Drupal7\AbstractHandler;
//use Drupal\ReferencesGenerator\Generator\GeneratorInterface;
use Drupal\ReferencesGenerator\Generator\EntityGenerator;

/**
 * Node reference field generator for Drupal 7.
 */
class NodeReferenceGenerator extends EntityGenerator {

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
    $entity_type = 'node';
    $entity_info = entity_get_info($entity_type);
    $return = array();
    $nid = db_select($entity_info['base table'], 't')
      ->fields('t', array($entity_info['entity keys']['id']))
      ->condition('t.' . $entity_info['entity keys']['label'], $value)
      ->execute()->fetchField();
    if ($nid) {
      $return[$this->language][] = array('nid' => $nid);
    }

    return $return;
  }

  /**
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   */
  public function create($field, $value) {
    $type = array_filter($field['settings']['referenceable_types']);
    $node = (object) array(
      'title' => $value,
      'type' => reset($type),
    );

    return $this->drupalContext->nodeCreate($node);
  }
}
