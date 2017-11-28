<?php
namespace Drupal\ReferencesGenerator\Generator\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\TaxonomyTermReferenceHandler;
use Drupal\Driver\Fields\Drupal7\AbstractHandler;

/**
 * Taxonomy term reference field generator for Drupal 7.
 */
class TaxonomyTermReferenceGenerator extends AbstractHandler {

  private $drupalContext;

  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  public function setDrupalContext(DrupalContext $drupalContext) {
    $this->drupalContext = $drupalContext;
  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {

  }

  /**
   * Attempt to determine the vocabulary for which the field is configured.
   *
   * @return mixed
   *   Returns a string containing the vocabulary in which the term must be
   *   found or NULL if unable to determine.
   */
  protected function getVocab() {
    if (!empty($this->fieldInfo['settings']['allowed_values'][0]['vocabulary'])) {
      return $this->fieldInfo['settings']['allowed_values'][0]['vocabulary'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function referenceExists($name) {
    $return = array();
    $terms = taxonomy_get_term_by_name($name, $this->getVocab());
    if (!empty($terms)) {
      $return[$this->language][] = array('tid' => array_shift($terms)->tid);
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
  public function create($field, $value) {
    $fieldName = $field['field_name'];
    if ($field['type'] !== 'taxonomy_term_reference') {
      throw new \Exception(sprintf("Invalid content type %s for field %s", $field['type'], $fieldName));
    }

    $vocabName = $this->getVocab();
    if ($vocabulary = taxonomy_vocabulary_machine_name_load($vocabName)) {
      $term = new \stdClass();
      $term->name = $value;
      $term->path = array('pathauto' => 1);
      $term->vid = $vocabulary->vid;
      $term = $this->drupalContext->termCreate($term);
      return $term->tid;
    }
    else {
      throw new \Exception(sprintf("Invalid vocabulary %s for field %s", $vocabName, $fieldName));
    }
  }
}
