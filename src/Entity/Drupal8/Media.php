<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\Drupal8;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\AbstractEntity;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\ImageGenerator;
use Drupal\media\Entity\Media as MediaCore;
use Drupal\media_entity\Entity\Media as MediaContrib;
use Drupal\file\Entity\File;

/**
 * Media creation for Drupal 8.
 */
class Media extends AbstractEntity {
  /**
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Save the image entity.
   */
  public function save() {
    $local_filename = ImageGenerator::createImage($this->data->filename);

    // Create the file.
    $file = File::create([
      'uri' => $local_filename,
      'uid' => 1,
    ]);
    $file->save();

    // Copy the file to public folder.
    $public_uri = 'public://' . $this->data->filename;
    $file = file_move($file, $public_uri);
    $fid = $file->fid;

    // Create media image entity
    $image_data = [
      'bundle' => 'image',
      'name' => $this->data->filename,
      'field_media_file' => [
        'target_id' => $file->id(),
      ],
    ];
    // Support Core and Contrib media.
    if (class_exists('Drupal\media\Entity\Media')) {
      $this->entity = MediaCore::create($image_data);
    }
    else {
      $this->entity = MediaContrib::create($image_data);
    }

    $this->entity->save();

    return $this->entity;
  }

  /**
   * Delete the media entity.
   *
   * @return mixed|void
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete() {
    $this->entity->delete();
  }
}
