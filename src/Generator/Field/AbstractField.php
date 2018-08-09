<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Field;

abstract class AbstractField implements FieldInterface {
  /**
   * @var string
   */
  protected $fieldName;

  /**
   * @var string
   */
  protected $entityType;

  /**
   * {@inheritdoc}
   */
  public function __construct($entity_type, $field_name) {
    if (empty($field_name)) {
      throw new \Exception('Empty field name');
    }
    $this->entityType = $entity_type;
    $this->fieldName = $field_name;
  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {
  }

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
  }
}
