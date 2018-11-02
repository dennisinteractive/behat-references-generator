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

  /**
   * Get entity type ID.
   * @return string
   */
  public function getEntityTypeId();

  /**
   * Get target bundle.
   *
   * @return string
   */
  public function getTargetBundle();

  /**
   * Retrieves bundles for which the field is configured to reference.
   *
   * @return mixed
   *   Array of bundle names, or NULL if not able to determine bundles.
   */
  public function getTargetBundles();

  /**
   * Get target bundle key.
   *
   * @return null|string
   */
  public function getTargetBundleKey();

}
