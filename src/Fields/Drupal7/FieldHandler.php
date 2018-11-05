<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\Drupal7;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\FieldHandlerInterface;
use Drupal\Driver\Fields\Drupal8\DefaultHandler;

class FieldHandler extends DefaultHandler implements FieldHandlerInterface {
  /**
   * @inheritdoc
   */
  public function getType() {
    if ($type = $this->getFieldInfo()->getType()) {
      return $type;
    }
  }

  /**
   * @inheritdoc
   */
  public function getFieldInfo() {
    return $this->fieldInfo;
  }

  /**
   * @inheritdoc
   */
  public function getFieldConfig() {
    return $this->fieldConfig;
  }
}
