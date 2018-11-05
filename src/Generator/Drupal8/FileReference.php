<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal8;

/**
 * File reference field generator for Drupal 8.
 */
class FileReference extends EntityReference {
  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
    $entity_type_id = $this->getEntityTypeId();
    $label_key = $this->getLabelKey();
    $query = \Drupal::entityQuery($entity_type_id)
      ->condition($label_key, $value)
      ->range(0, 1);
    $query->accessCheck(FALSE);

    if ($entities = $query->execute()) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Creates a file.
   *
   * @param $value
   *
   * @return mixed
   */
  public function create($value) {
    $entity_type_id = $this->getEntityTypeId();
    $target_bundle = $this->getTargetBundle();

    $entity = (object) array(
      $this->getLabelKey() => $value,
      'type' => $entity_type_id,
      $this->getTargetBundleKey() => $target_bundle,
    );

    $this->getEntityManager()->createEntity($entity_type_id, $target_bundle, $entity);
  }

  /**
   * Get label key.
   *
   * @return string
   */
  protected function getLabelKey() {
    $entity_type_id = $this->getEntityTypeId();
    $entity_definition = \Drupal::entityManager()->getDefinition($entity_type_id);

    $label_key = $entity_definition->getKey('label');

    return $label_key;
  }

}
