<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7\Field;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Field\AbstractField;

class Field extends AbstractField {
  /**
   * @inheritdoc
   */
  public function getType() {
    $field = field_read_field($this->fieldName);
    return $field['type'];
  }
}
