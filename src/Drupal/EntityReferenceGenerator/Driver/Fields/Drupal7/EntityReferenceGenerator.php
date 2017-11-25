<?php
namespace Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\EntityreferenceHandler;

/**
 * Entity reference field generator for Drupal 7.
 */
class EntityReferenceGenerator extends EntityreferenceHandler {

  /**
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   */
  public function createReferencedItem($field, $value) {

  }
}
