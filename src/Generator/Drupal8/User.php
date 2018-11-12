<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal8;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\AbstractGenerator;

/**
 * Entity reference field generator for Drupal 8.
 */
class User extends AbstractGenerator {

  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
    return (bool) \Drupal::entityQuery('user')
      ->condition('name', $value)
      ->execute();
  }

  /**
   * Creates missing references.
   *
   * @param $field
   * @param $value
   *
   * @return mixed
   *
   * @todo delete user after every scenario.
   */
  public function create($value) {
    foreach ($this->getGeneratorManager()->getDefaultUser() as $role => $names) {
      foreach ($names as $name) {
        if ($name == $value) {
          $user = (object) array(
            'name' => $value,
            'roles' => [$role],
            'status' => 1,
          );
          break;
        }
      }
    }

    if (isset($user)) {
      return $this->getDrupal()->getDriver('drupal')->userCreate($user);
    }
  }

}
