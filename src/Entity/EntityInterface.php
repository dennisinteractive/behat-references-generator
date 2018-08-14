<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

/**
 * Entity Interface.
 */
Interface EntityInterface {
  /**
   * Save the entity.
   *
   * @return mixed
   */
  public function save();

  /**
   * Delete the entity.
   *
   * @return mixed
   */
  public function delete();
}
