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
  public function __construct($entityType, $defaultContentOverrides = array()) {
    $this->entityType = $entityType;
    $this->override($defaultContentOverrides);
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

  /**
   * Gets default values for fields.
   * We use machine names here.
   * @todo call an event to load defaults instead of this array
   *
   * @param string $bundleName
   *    The bundle name i.e. node.
   *
   * @return array
   */
  public function defaultContent() {
    $defaultContent = array(
      'image' => array(
        'filename' => 'Default image.jpg',
        'credits' => 'Default credits',
        'description' => 'Default description',
        'text' => 'Default title',
        'alt_text' => 'Default alt text',
      ),
      'term' => array(
        // @todo this is not working.
        'tags' => array(
          'name' => 'Default Term',
          'alias' => 'default-term',
        ),
      ),
      'node' => array(
        'author' => array(
          'title' => 'Default author title',
          'body' => 'Default author Body',
          'field_author_first_name' => 'Default author name',
          'field_author_last_name' => 'Default author surname',
          'status' => 1,
        ),
        'article' => array(
          'title' => 'Default Article title',
          'body' => 'Default Article body',
          'field_sponsored' => '0',
          'field_short_title' => 'Default short title',
          'field_short_teaser' => 'Default short teaser',
          'field_article_type' => 'Default article type',
          'field_main_purpose' => 'Default content purpose',
          'field_category_primary' => 'Default category',
          'field_author' => 'Default Author1, Default Author2',
          'field_tags' => 'Default Tag1, Default Tag2, Default Tag3',
          'field_primary_image' => 'Default image.jpg',
          'alias' => 'default-article',
          'status' => 1,
        ),
        'page' => array(
          'title' => 'Default Page title',
          'body' => 'Default Page body',
          'alias' => 'default-page',
          'status' => 1,
          'promote' => 1,
        ),
        'test' => array(
          'title' => 'Default Test title',
          'body' => 'Default Test body',
          'alias' => 'default-test',
          'status' => 1,
        ),
        'review' => array(
          'title' => 'Default Review title',
          'body' => 'Default Review body',
          'alias' => 'default-review',
          'status' => 1,
        ),
        'gallery_adv' => array(
          'title' => 'Default Gallery title',
          'body' => 'Default Gallery body',
          'alias' => 'default-gallery',
          'field_gallery_files' => 'gal_image_1.jpg, gal_image_2.jpg',
          'status' => 1,
        ),
      ),
    );

    return $defaultContent;
  }

  /**
   * Reads the overrides from behat.yml and updates the default content.
   * We use machine names for field overrides on behat.yml.
   *
   * @param array $defaultContentOverrides
   *    The overrides to replace items on default content.
   */
  private function override($defaultContentOverrides = array()) {
    $this->defaultContent = $this->defaultContent();
    if (!empty($defaultContentOverrides)) {
      $this->defaultContent = array_replace_recursive($this->defaultContent, $defaultContentOverrides);
    }

    return $this->defaultContent;
  }

}
