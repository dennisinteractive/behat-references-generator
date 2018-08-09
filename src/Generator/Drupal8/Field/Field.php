<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal8\Field;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Field\AbstractField;

class Field extends AbstractField {
  /**
   * @inheritdoc
   */
  public function getType() {
    $config_name = 'field.storage.' . $this->entityType . '.' . $this->fieldName;
    $config = \Drupal::configFactory()->get($config_name);
    if ($type = $config->get('type')) {
      return $type;
    }
  }
}
