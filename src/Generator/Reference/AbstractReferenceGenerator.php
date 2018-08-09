<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Reference;

use Drupal\Driver\Fields\Drupal7\AbstractHandler;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Context\ReferencesGeneratorContext;

abstract class AbstractReferenceGenerator extends AbstractHandler implements ReferenceGeneratorInterface {
  /**
   * @var ReferencesGeneratorContext
   */
  protected $referencesGeneratorContext;

  /**
   * {@inheritdoc}
   */
  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
  }

  /**
   * @param ReferencesGeneratorContext $drupalContext
   */
  public function setReferencesContext(ReferencesGeneratorContext $referencesGeneratorContext) {
    $this->referencesGeneratorContext = $referencesGeneratorContext;
  }

  /**
   * {@inheritdoc}
   */
  public function expand($values) {
  }

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
  }
}
