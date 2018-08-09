<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7\Reference;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Reference\AbstractReferenceGenerator;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7\Entity\Image;

/**
 * File field generator for Drupal 7.
 */
class File extends AbstractReferenceGenerator {

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
    $return = array();

    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'file')
      ->propertyCondition('filename', $value)
      ->propertyOrderBy('timestamp', 'DESC')
      ->range(0, 1);

    $result = $query->execute();

    if (!empty($result['file'])) {
      $files = entity_load('file', array_keys($result['file']));
      $file = current($files);

      $return[$this->language][] = array(
        'filename' => $file->filename,
        'uri' => $file->uri,
        'fid' => $file->fid,
        'display' => 1,
      );
    }

    return $return;
  }

  /**
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   */
  public function create($field, $value) {
    switch ($field['type']) {
      case 'image':
        $default = new DefaultContent('image', array()); // @todo pass the default values $this->defaultContentMapping
        $defaultImage = $default->getContent();
        $defaultImage['filename'] = $value;
        $this->createImage($defaultImage);
        break;
    }
  }

  /**
   * Create an image entity.
   *
   * @param $image
   * @return mixed
   * @throws \Exception
   */
  protected function createImage($image) {
    $file = new Image();
    return $file->save();
  }

}
