<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal8\Entity;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\ImageGenerator;
use Drupal\media\Entity\Media;
use Drupal\media_entity\Entity\Media as MediaContrib;
use Drupal\file\Entity\File;

/**
 * Image creation for Drupal 8.
 */
class Image {
  /**
   * @var array
   */
  protected $data;

  /**
   * Create an image entity.
   *
   * @param $data
   * @throws \Exception
   */
  public function __construct($data) {
    $this->data = $data;
  }

  /**
   * Save the image entity.
   */
  public function save() {
    $image = $this->data;
    $local_filename = ImageGenerator::createImage($image);

    // Create the file.
    $file = File::create([
      'uri' => $local_filename,
      'uid' => 1,
    ]);
    $file->save();

    // Copy the file to public folder.
    $public_uri = 'public://' . $image['filename'];
    $file = file_move($file, $public_uri);
    $fid = $file->fid;

    // Create media image entity
    $image_data = [
      'bundle' => 'image',
      'name' => $this->data['filename'],
      'field_media_file' => [
        'target_id' => $file->id(),
      ],
    ];
    // Support Core and Contrib media.
    if (class_exists('Drupal\media\Entity\Media')) {
      $entity = Media::create($image_data);
    }
    else {
      $entity = MediaContrib::create($image_data);
    }

    return $entity;
  }

}
