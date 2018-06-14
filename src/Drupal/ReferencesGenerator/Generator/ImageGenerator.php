<?php

namespace Drupal\ReferencesGenerator\Generator;

class ImageGenerator  {

  public static function createImage($image) {
    $local_filename = '/tmp/' . $image['filename'];

    // Create a blank image and add some text
    $im = imagecreatetruecolor(200, 200);
    $text_color = imagecolorallocate($im, 0, 0, 255);
    imagestring($im, 5, 60, 90, $image['text'], $text_color);

    $color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($im, 0, 0, $color);

    // Save the image as 'simpletext.jpg'
    imagejpeg($im, $local_filename);

    // Free up memory
    imagedestroy($im);

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
    $info = entity_get_info('file');
    $info['save callback']($entity[$fid]);

    return $entity[$fid];
  }
}
