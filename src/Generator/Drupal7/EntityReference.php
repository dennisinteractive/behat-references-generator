<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\AbstractGenerator;

/**
 * Entity reference field generator for Drupal 7.
 */
class EntityReference extends AbstractGenerator {
  /**
   * @inheritdoc
   */
  public function referenceExists($value) {
    $entity_type = $this->fieldInfo['settings']['target_type'];
    $entity_info = entity_get_info($entity_type);

    // For users set label to username.
    if ($entity_type == 'user') {
      $entity_info['entity keys']['label'] = 'name';
    }

    $return = array();
    $target_id = db_select($entity_info['base table'], 't')
      ->fields('t', array($entity_info['entity keys']['id']))
      ->condition('t.' . $entity_info['entity keys']['label'], $value)
      ->execute()->fetchField();
    if ($target_id) {
      $return[$this->language][] = array('target_id' => $target_id);
    }

    return $return;
  }

  /**
   * @inheritdoc
   */
  public function create($value) {
    $entity_type_id = $this->getEntityTypeId();
    // @todo support any entity allowed in field.
    //$entity_type_id = array_filter($field['settings']['handler_settings']['target_bundles']);
    $node = (object) array(
      'title' => $value,
      'type' => $entity_type_id,
    );

    $this->getEntityManager()->createEntity('node', $node->type, $node);
  }
}
