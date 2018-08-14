<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

class ImageGenerator  {

  /**
   * Generate a placeholder image.
   *
   * @param $image
   * @return string
   */
  public static function createImage($filename, $text = 'Behat Image') {
    $local_filename = '/tmp/' . $filename;

    // Create a blank image and add some text
    $im = imagecreatetruecolor(200, 200);
    $text_color = imagecolorallocate($im, 0, 0, 255);
    imagestring($im, 5, 60, 90, $text, $text_color);

    $color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($im, 0, 0, $color);

    // Save the image as 'simpletext.jpg'
    imagejpeg($im, $local_filename);

    // Free up memory
    imagedestroy($im);

    return $local_filename;
  }
}
