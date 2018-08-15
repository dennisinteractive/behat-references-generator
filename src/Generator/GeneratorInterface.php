<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Context\ReferencesGeneratorContext;

interface GeneratorInterface {
  /**
   * Check if a reference exists.
   *
   * @param $value
   * @return bool
   */
  public function referenceExists($value);

  /**
   * Create referenced content.
   *
   * @param $values
   */
  public function create($values);

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Fields\FieldHandlerInterface;
   */
  public function getFieldHandler();

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager
   */
  public function getGeneratorManager();

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager
   */
  public function getEntityManager();
}
