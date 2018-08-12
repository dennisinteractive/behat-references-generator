<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Entity;

/**
 * Abstract Entity.
 */
abstract class AbstractEntity implements EntityInterface {
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

}
