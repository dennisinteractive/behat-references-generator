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

  protected $type;

  /**
   * DefaultContent constructor.
   *
   * @param $type Content type.
   */
  public function __construct($type) {
    $this->type = $type;
  }
//
//  /**
//   * Returns default content.
//   *
//   * @param $entity
//   *
//   * @return array
//   */
//  private function getDefaultEntityValues($entity) {
//    switch ($entity->entityType) {
//      case 'node':
//        return $this->getDefaultNode($entity->type);
//        break;
//      case 'term':
//        return $this->getDefaultTerm();
////        $defaultTerm = new DefaultTaxononyTerm();
////        $vocabName = taxonomy_vocabulary_load($entity->vid);
////        return $defaultTerm->getDefaultContent($vocabName);
//        break;
//    }
//  }

  /**
   * Gets default values for fields.
   *
   * @param      $type Content type.
   * @param null $bundleName Bundle name.
   *
   * @return array
   */
  public function mapping($bundleName = NULL) {
    $defaultContent = array();

    switch ($this->type) {
      case 'image':
        $defaultContent = array(
          'filename' => 'bddtest.jpg',
          'credits' => 'By Dennis Publishing',
          'description' => 'This is an image description',
          'text' => 'BDD test',
          'alt_text' => 'This is an alt test',
        );
        break;

      case 'term':
        $defaultContent = array(
          'name' => 'BDD Term',
          'alias' => 'bdd-term',
          //'path' => array('alias' => 'bdd-term', 'pathauto' => 0),
        );
        break;

      case 'node':
        switch ($bundleName) {
          case 'author':
            $defaultContent = array(
              'title' => 'BDD Default author',
              'body' => 'BDD Author Body',
              'field_author_first_name' => 'BDD author name',
              'field_author_last_name' => 'BDD author surname',
              'status' => 1,
            );
            break;

          case 'article':
          case 'review':
          case 'gallery_adv':
          default:
            $defaultContent = array(
              'title' => sprintf('BDD Default %s content test', $bundleName),
              'body' => 'BDD Body',
              'field_sponsored' => '0',
              'field_short_teaser' => 'BDD Short teaser',
              'field_article_type' => 'BDD Article type',
              'field_main_purpose' => 'BDD content purpose',
              'field_category_primary' => 'BDD Category',
              'field_author' => 'BDD Author1, BDD Author2',
              //'field_gallery_files' => 'gal_image_1.jpg, gal_image_2.jpg',
              'field_tags' => 'BDD Tag1, BDD Tag2, BDD Tag3',
              'field_primary_image' => 'bddtest.jpg',
              'alias' => sprintf('bdd-default-%s-content-test', $bundleName),
              'status' => 1,
            );
            $defaultContent['field_short_title'] = $defaultContent['title'];
        }
        break;

    }

    return $defaultContent;
  }
}
