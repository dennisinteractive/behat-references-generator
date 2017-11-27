<?php

namespace Drupal\EntityReferenceGenerator\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\EntityReferenceGenerator\Content\DefaultContent;

trait ImageGeneratorContext {

  /**
   * @Given I have an image
   */
  public function defaultImage() {
    $default = new DefaultContent();
    $defaultImage = $this->defaultContent = $default->image;
    $nodesTable = $this->getTableNode($defaultImage);

    return $this->iCreateAFile($nodesTable);

  }

  /**
   * @Given I create a file
   */
  public function iCreateAFile(TableNode $nodesTable) {
    $default = new DefaultContent();
    $defaultImage = $this->defaultContent = $default->image;
    $defaultImage['text'] = 'BDD TEST';

    // Create images from the first row of table data.
    $table_items = $nodesTable->getHash()[0];

    // Change all the keys to lowercase.
    $table_items = (array_change_key_case($table_items, CASE_LOWER));

    // Allow text to be overriden.
    if (isset($table_items['text'])) {
      $defaultImage['text'] = $table_items['text'];
    }

    // Allow Credits to be overriden.
    if (isset($table_items['credits'])) {
      $defaultImage['credits'] = $table_items['credits'];
    }

    // Allow Description to be overriden.
    if (isset($table_items['description'])) {
      $defaultImage['description'] = $table_items['description'];
    }

    // Allow Alt text to be overriden.
    if (isset($table_items['file_alt_text'])) {
      $defaultImage['alt_text'] = $table_items['file_alt_text'];
    }

    // Allow filename to be overriden.
    if (isset($table_items['filename'])) {
      $defaultImage['filename'] = $table_items['filename'];
    }
    $local_filename = '/tmp/' . $defaultImage['filename'];

    // Create a blank image and add some text
    $im = imagecreatetruecolor(200, 200);
    $text_color = imagecolorallocate($im, 0, 0, 255);
    imagestring($im, 5, 60, 90, $defaultImage['text'], $text_color);

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
    $public_uri = 'public://' . $defaultImage['filename'];
    $file = file_copy($dfile, $public_uri);
    $fid = $file->fid;
    $this->cleanFids[] = $fid;

    $mediafiles = entity_load('file', array($fid));

    // Change fields.
    $mediafiles[$fid]->filename = $defaultImage['filename'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['value'] = $defaultImage['credits'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['credits'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['format'] = 'plain_text';
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['value'] = $defaultImage['description'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['description'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['value'] = $defaultImage['alt_text'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['alt_text'];

    // Save the attributes.
    entity_save('file', $mediafiles[$fid]);

    return ($mediafiles[$fid]);
  }


  /**
   * @Then the file :image should be available
   */
  public function theFileShouldBeAvailable($image) {
    $path = file_create_url('public://' . $image);
    $this->drupalContext->getSession()->visit($path);
    if ($this->drupalContext->getSession()->getStatusCode() !== 200) {
      throw new \Exception(sprintf('Could not find image on %s', $path));
    };
  }

}
