<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

/**
 * Taxonomy Term.
 */
class TaxonomyTerm extends AbstractEntity {
  /**
   * @inheritdoc
   */
  public function save() {
    return $this->getDrupal()->getDriver()->createTerm($this->data);
  }

  /**
   * @inheritdoc
   */
  public function delete() {
    return $this->getDrupal()->getDriver()->termDelete($this->data);
  }
}
