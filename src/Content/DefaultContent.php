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
   * Default properties required when creating an entity.
   *
   * @var array
   */
  protected $defaultProperties = [
    'node' => [
      'title' => 'Behat Test Node',
      'status' => '1',
      'author' => 'BDD Author',
    ],
    'taxonomy_term' => [
      'name' => 'Behat Test Term',
      'status' => '1',
    ],
    'media' => [
      'name' => 'behat_test_media_image.jpg',
      'status' => '1',
    ],
    'file' => [
      'filename' => 'behat_test_file_image.jpg',
      'status' => '1',
    ],
  ];

  /**
   * DefaultContent constructor.
   *
   * @param array $default_content
   */
  public function __construct($default_content = array()) {
    $this->defaultContent = $default_content;
  }

  /**
   * Returns the default content.
   *
   * @param $entity_type
   * @param $bundle_name
   *
   * @return array
   */
  public function getContent($entity_type, $bundle_name) {
    $default_properties = isset($this->defaultProperties[$entity_type]) ? $this->defaultProperties[$entity_type] : [];
    if (isset($this->defaultContent[$entity_type][$bundle_name])) {
      $default_content = $this->defaultContent[$entity_type][$bundle_name];
      return array_merge($default_properties, $default_content);
    }
    return $default_properties;
  }

}
