<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\AbstractGenerator;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\Drupal7\Image;

/**
 * File field generator for Drupal 7.
 */
class FileReference extends AbstractGenerator {
  /**
   * @inheritdoc
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
   * @inheritdoc
   */
  public function create($field, $value) {
    $entity_type_id = $this->getEntityTypeId();
    //switch ($field['type']) {
    switch ($entity_type_id) {
      case 'image':
        $this->getEntityManager()->createEntity('file', 'image', []);
        break;
    }
  }
}
