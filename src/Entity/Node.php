<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

/**
 * Node.
 */
class Node extends AbstractEntity {
  /**
   * @inheritdoc
   */
  public function save() {
    return $this->getDrupal()->getDriver()->createNode($this->data);
  }

  /**
   * @inheritdoc
   */
  public function delete() {
    return $this->getDrupal()->getDriver()->nodeDelete($this->data);
  }
}
