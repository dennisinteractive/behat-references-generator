<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Content;

/**
 * Class DefaultContent
 *
 * @todo This content will be loaded from a yml file.
 *
 * @package Drupal\ReferencesGenerator\Content
 */
class DefaultContent {
  /**
   * Stores the default content mapping.
   *
   * @var $defaultContent
   */
  protected $defaultContent;

  /**
   * DefaultContent constructor.
   *
   * @param array $defaultContentOverrides
   *    The overrides for the default content.
   */
  public function __construct($default_content = array()) {
    $this->defaultContent = $default_content;
  }

  /**
   * Returns the default content.
   *
   * @param string $bundleName
   *    The bundle name i.e. node.
   *
   * @return mixed
   */
  public function getContent($entity_type, $bundle_name = NULL) {
    if (isset($bundle_name)) {
      if (isset($this->defaultContent[$entity_type][$bundle_name])) {
        return $this->defaultContent[$entity_type][$bundle_name];
      }
      else {
        return array();
      }
    }

    if (isset($this->defaultContent[$entity_type])) {
      return $this->defaultContent[$entity_type];
    }
  }

}
