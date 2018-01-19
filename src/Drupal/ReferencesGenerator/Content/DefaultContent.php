<?php

namespace Drupal\ReferencesGenerator\Content;

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
   *
   * @param string $bundleName
   *    The bundle name i.e. node.
   *
   * @return array
   */
  public function defaultContent() {
    $defaultContent = array(
      'image' => array(
        'filename' => 'bddtest.jpg',
        'credits' => 'By Dennis Publishing',
        'description' => 'This is an image description',
        'text' => 'BDD test',
        'alt_text' => 'This is an alt test',
      ),
      'term' => array(
        'name' => 'BDD Term',
        'alias' => 'bdd-term',
        //'path' => array('alias' => 'bdd-term', 'pathauto' => 0),
      ),
      'node' => array(
        'author' => array(
          'title' => 'BDD Default author',
          'body' => 'BDD Author Body',
          'field_author_first_name' => 'BDD author name',
          'field_author_last_name' => 'BDD author surname',
          'status' => 1,
        ),
        'article' => array(
          'title' => 'BDD Default %bundle_name content test',
          'body' => 'BDD Body',
          'field_sponsored' => '0',
          'field_short_teaser' => 'BDD Short teaser',
          'field_article_type' => 'BDD Article type',
          'field_main_purpose' => 'BDD content purpose',
          'field_category_primary' => 'BDD Category',
          'field_author' => 'BDD Author1, BDD Author2',
          'field_tags' => 'BDD Tag1, BDD Tag2, BDD Tag3',
          'field_primary_image' => 'bddtest.jpg',
          'alias' => 'bdd-default-%bundle_name-content-test',
          'status' => 1,
        ),
      ),
    );

    // Copy title.
    $defaultContent['node']['article']['field_short_title'] = $defaultContent['node']['article']['title'];

    // Copy some defaults to other content types.
    $defaultContent['node']['test'] = $defaultContent['node']['article'];
    $defaultContent['node']['page'] = $defaultContent['node']['article'];
    $defaultContent['node']['review'] = $defaultContent['node']['article'];
    $defaultContent['node']['gallery_adv'] = $defaultContent['node']['article'];

    // Content specific fields.
    $defaultContent['node']['gallery_adv']['field_gallery_files'] = 'gal_image_1.jpg, gal_image_2.jpg';

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
