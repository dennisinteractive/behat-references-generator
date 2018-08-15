<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\Drupal8;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\AbstractEntity;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\ImageGenerator;
use Drupal\file\Entity\File as FileCore;

/**
 * File creation for Drupal 8.
 */
class File extends AbstractEntity {
  /**
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Save the image entity.
   */
  public function save() {
    $filename = $this->data->filename;
    $local_filename = ImageGenerator::createImage($filename);

    // Create the file.
    $file_data = (array) $this->data;
    $file_data['uri'] = $local_filename;
    $file_data['uid'] = 1;

    $this->entity = FileCore::create($file_data);
    $this->entity->save();

    // Copy the file to public folder.
    $public_uri = 'public://' . $filename;
    $this->entity = file_move($this->entity, $public_uri);

    return $this->entity;
  }

  /**
   * Delete the file entity.
   *
   * @return mixed|void
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete() {
    $this->entity->delete();
  }
}
