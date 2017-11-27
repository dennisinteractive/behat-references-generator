<?php

namespace Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7;

use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\FileHandler;

/**
 * File field generator for Drupal 7.
 */
class FileGenerator extends FileHandler {
  private $drupalContext;

  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

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
  public function createReferencedItem($field, $value) {
    print "Need to implement File generator";
    ob_flush();
  }

  /**
   * @Given I have an image
   */
  public function defaultImage() {
    $defaults = $this->getDefaultImageArray();
    $nodesTable = $this->getTableNode($defaults);

    return $this->iCreateAFile($nodesTable);

  }
}
