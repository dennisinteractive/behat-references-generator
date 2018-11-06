<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\AbstractGenerator;

/**
 * Node reference field generator for Drupal 7.
 */
class NodeReference extends AbstractGenerator {

  /**
   * @inheritdoc
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
   * @inheritdoc
   */
  public function create($value) {
    $entity_type_id = $this->getEntityTypeId();
    //$type = array_filter($field['settings']['referenceable_types']);
    $node = (object) array(
      'title' => $value,
      'type' => $entity_type_id,
    );

    return $this->getEntityManager()->createEntity('node', $node->type, $node);
  }
}
