<?php

namespace Drupal\EntityReferenceGenerator\Driver\Field;

use Drupal\Driver\Fields\FieldHandlerInterface;

/**
 * Node reference field handler for Drupal 7.
 *
 * Note: This class doesn't get called automatically because the caller has some
 * hard coded paths, see Drupal\Driver\Cores\AbstractCore.
 * Since there is no obvious way to extend the field handlers, we are forking
 * the drupal driver. Leaving this class here until we figure out another way of
 * making it discoverable.
 */
class NodereferenceHandler implements FieldHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public function expand($values) {
    $entity_type = 'node';
    $entity_info = entity_get_info($entity_type);
    $return = array();
    foreach ($values as $value) {
      $nid = db_select($entity_info['base table'], 't')
        ->fields('t', array($entity_info['entity keys']['id']))
        ->condition('t.' . $entity_info['entity keys']['label'], $value)
        ->execute()->fetchField();
      if ($nid) {
        $return[$this->language][] = array('nid' => $nid);
      }
    }

    return $return;
  }
}
