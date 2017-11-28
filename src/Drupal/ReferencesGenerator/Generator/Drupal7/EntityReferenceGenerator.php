<?php

namespace Drupal\ReferencesGenerator\Generator\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\AbstractHandler;

/**
 * Entity reference field generator for Drupal 7.
 */
class EntityReferenceGenerator extends AbstractHandler {
  private $drupalContext;

  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {

  }

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

    return $this->drupalContext->nodeCreate($node);
  }
}
