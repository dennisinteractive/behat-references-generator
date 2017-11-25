<?php
namespace Drupal\EntityReferenceGenerator\Driver\Fields\Drupal7;

use Drupal\Driver\Fields\FieldHandlerInterface;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\TaxonomyTermReferenceHandler;

/**
 * Taxonomy term reference field generator for Drupal 7.
 */
class TaxonomyTermReferenceGenerator extends TaxonomyTermReferenceHandler {

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
  public function referenceExists($name) {
    // echo 'Searching term ' . $name . PHP_EOL;ob_flush();
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
  public function createReferencedItem($field, $value) {
    $fieldName = $field['field_name'];
    if ($field['type'] !== 'taxonomy_term_reference') {
      throw new \Exception(sprintf("Invalid content type %s for field %s", $field['type'], $fieldName));
    }

//    $key = array_keys($field['settings']['allowed_values']);
//    $key = reset($key);
    $vocabName = $this->getVocab();//$field['settings']['allowed_values'][$key]['vocabulary'];
    if ($vocabulary = taxonomy_vocabulary_machine_name_load($vocabName)) {
      // echo 'Creating term for vocab ' . $this->getVocab(). PHP_EOL;ob_flush();
      $term = new \stdClass();
      $term->name = $value;
      $term->path = array('pathauto' => 1);
      $term->vid = $vocabulary->vid;

      // print_r($term);ob_flush();
      // Create the term so that drupal context will mark it for deletion in @AfterScenario.
      $term = $this->drupalContext->termCreate($term);
      // echo 'CREATED TERM ' . $term->name . '(' . $term->tid . ')' . PHP_EOL; ob_flush();
      return $term->tid;
    }
    else {
      throw new \Exception(sprintf("Invalid vocabulary %s for field %s", $vocabName, $fieldName));
    }
  }
}
