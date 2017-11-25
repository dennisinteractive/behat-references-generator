<?php
namespace Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\NodereferenceHandler;

/**
 * Node reference field generator for Drupal 7.
 */
class NodeReferenceGenerator extends NodereferenceHandler {

  private $drupalContext;

  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

  public function referenceExists($value) {
    return $this->expand(array($value));
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
    $type = array_filter($field['settings']['referenceable_types']);
    $node = (object) array(
      'title' => $value,
      'type' => reset($type),
    );
    return $this->drupalContext->nodeCreate($node);
  }
}
