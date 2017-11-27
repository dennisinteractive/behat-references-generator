<?php
namespace Drupal\ReferencesGenerator\Driver\Fields\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\EntityreferenceHandler;

/**
 * Entity reference field generator for Drupal 7.
 */
class EntityReferenceGenerator extends EntityreferenceHandler {

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
    $type = array_filter($field['settings']['handler_settings']['target_bundles']);
    $node = (object) array(
      'title' => $value,
      'type' => reset($type),
    );
    return $this->drupalContext->nodeCreate($node);
  }
}
