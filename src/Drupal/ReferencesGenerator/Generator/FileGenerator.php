<?php

namespace Drupal\ReferencesGenerator\Generator;

class FileGenerator  {

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

    $mediafiles = entity_load('file', array($fid));

    // Change fields.
    $mediafiles[$fid]->filename = $image['filename'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['value'] = $image['credits'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['safe_value'] = $image['credits'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['format'] = 'plain_text';
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['value'] = $image['description'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['safe_value'] = $image['description'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['value'] = $image['alt_text'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['safe_value'] = $image['alt_text'];

    // Save the attributes.
    entity_save('file', $mediafiles[$fid]);

    return $mediafiles[$fid];
  }
}
