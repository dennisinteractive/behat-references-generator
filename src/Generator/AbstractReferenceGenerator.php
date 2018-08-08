<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\Driver\Fields\Drupal7\AbstractHandler;

abstract class AbstractReferenceGenerator extends AbstractHandler implements ReferenceGeneratorInterface {
  /**
   * {@inheritdoc}
   */
  public function __construct(\stdClass $entity, $entity_type, $field_name) {
    parent::__construct($entity, $entity_type, $field_name);
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
