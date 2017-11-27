<?php

namespace Drupal\EntityReferenceGenerator\Content;

/**
 * Class DefaultContent
 *
 * @todo This content will be loaded from a yml file.
 *
 * @package Drupal\EntityReferenceGenerator\Content
 */
class DefaultContent {
  public function __get($type) {
    $defaultContent = array();

    switch ($type) {
      case 'image':
        $defaultContent = array(
          'filename' => 'bddtest.jpg',
          'credits' => 'By Dennis Publishing',
          'description' => 'This is an image description',
          'text' => 'BDD test',
          'alt_text' => 'This is an alt test',
        );
        break;
    }

    return $defaultContent;
  }
}
