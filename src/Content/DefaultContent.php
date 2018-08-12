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
   * Stores the entity type.
   *
   * @var $entityType
   */
  protected $entityType;

  /**
   * Stores the default content mapping.
   *
   * @var $defaultContent
   */
  protected $defaultContent;

  /**
   * DefaultContent constructor.
   *
   * @param string $entityType
   *    The Content type i.e. article.
   * @param array $defaultContentOverrides
   *    The overrides for the default content.
   */
  public function __construct($entityType, $defaultContent = array()) {
    $this->entityType = $entityType;
    $this->defaultContent = $defaultContent;
  }

  /**
   * Returns the default content.
   *
   * @param string $bundleName
   *    The bundle name i.e. node.
   *
   * @return mixed
   */
  public function getContent($bundleName = NULL) {
    if (isset($bundleName)) {
      if (isset($this->defaultContent[$this->entityType][$bundleName])) {
        return $this->defaultContent[$this->entityType][$bundleName];
      }
      else {
        return array();
      }
    }

    if (isset($this->defaultContent[$this->entityType])) {
      return $this->defaultContent[$this->entityType];
    }
  }

}
