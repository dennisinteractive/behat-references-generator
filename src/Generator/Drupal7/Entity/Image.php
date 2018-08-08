<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal7\Entity;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\ImageGenerator;

/**
 * Image creation for Drupal 7.
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
   * Create the image entity.
   */
  public function save() {
    $image = $this->data;
    $local_filename = ImageGenerator::createImage($image);
    // Create a file object.
    $dfile = (object) array(
      'uri' => $local_filename,
      'filemime' => file_get_mimetype($local_filename),
      'status' => 1,
    );

    // Copy the file.
    $public_uri = 'public://' . $image['filename'];
    $file = file_copy($dfile, $public_uri);
    $fid = $file->fid;

    $entity = entity_load('file', array($fid));

    // Change fields.
    // @todo these fields are hard coded, need to use mapping from behat.yml
    $entity[$fid]->filename = $image['filename'];
    $entity[$fid]->field_file_credits[LANGUAGE_NONE][0]['value'] = $image['credits'];
    $entity[$fid]->field_file_credits[LANGUAGE_NONE][0]['safe_value'] = $image['credits'];
    $entity[$fid]->field_file_description[LANGUAGE_NONE][0]['format'] = 'plain_text';
    $entity[$fid]->field_file_description[LANGUAGE_NONE][0]['value'] = $image['description'];
    $entity[$fid]->field_file_description[LANGUAGE_NONE][0]['safe_value'] = $image['description'];
    $entity[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['value'] = $image['alt_text'];
    $entity[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['safe_value'] = $image['alt_text'];

    // Save the attributes.
//    $info = entity_get_info('file');
//    $info['save callback']($entity[$fid]);
    if (function_exists('entity_save')) {
      entity_save('file', $entity[$fid]);
    }
    else {
      throw new \Exception('Cannot save the entity. Please make sure Entity API is enabled.');
    }

    return $entity[$fid];
  }

}
