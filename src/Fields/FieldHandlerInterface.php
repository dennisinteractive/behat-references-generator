<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Fields;

interface FieldHandlerInterface {
  /**
   * Get the field type.
   *
   * @return string
   */
  public function getType();

  /**
   * @return \Drupal\Core\Field\FieldStorageDefinitionInterface|\Drupal\field\Entity\FieldStorageConfig
   */
  public function getFieldInfo();

  /**
   * @return \Drupal\Core\Field\FieldDefinitionInterface|\Drupal\field\Entity\FieldConfig
   */
  public function getFieldConfig();
}
