<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7\Reference;

/**
 * Entity reference field generator for Drupal 7.
 */
class Entity extends AbstractGenerator {
  /**
   * {@inheritdoc}
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
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   */
  public function create($field, $value) {
    $type = array_filter($field['settings']['handler_settings']['target_bundles']);
    $node = (object) array(
      'title' => $value,
      'type' => reset($type),
    );

    return $this->referencesGeneratorContext->nodeCreate($node);
  }
}
