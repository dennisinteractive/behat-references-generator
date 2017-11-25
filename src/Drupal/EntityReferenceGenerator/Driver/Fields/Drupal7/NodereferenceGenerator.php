<?php

namespace Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;

/**
 * Node reference field handler for Drupal 7.
 *
 * Note: This class doesn't get called automatically because the caller has some
 * hard coded paths, see Drupal\Driver\Cores\AbstractCore.
 * Since there is no obvious way to extend the field handlers, we are forking
 * the drupal driver. Leaving this class here until we figure out another way of
 * making it discoverable.
 */
class NodeReferenceGenerator implements FieldHandlerInterface {

  private $drupalContext;
//
//  public function __construct(DrupalContext $drupalContext) {
//    //parent::construct();
//    $this->drupalContext = $drupalContext;
//  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {
    die('aaa');
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

  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

  /**
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   */
  public function createReferencedItem($field, $value) {
    //$this->drupalContext->createNodes($type, TableNode $nodesTable);
    //return $this->createReferencedNode($field, $value);
    $type = array_filter($field['settings']['referenceable_types']);
    $node = (object) array(
      'title' => $value,
      'type' => reset($type),
    );
    return $this->drupalContext->nodeCreate($node);
  }
}
