<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Field;

interface FieldInterface {
  /**
   * Get the field type.
   *
   * @return string
   */
  public function getType();
}
