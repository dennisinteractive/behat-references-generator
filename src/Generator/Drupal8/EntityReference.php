<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Drupal8;

use Drupal\taxonomy\Entity\Vocabulary;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\AbstractGenerator;

/**
 * Entity reference field generator for Drupal 8.
 */
class EntityReference extends AbstractGenerator {
  /**
   * {@inheritdoc}
   */
  public function referenceExists($value) {
    $entity_type_id = $this->getEntityTypeId();
    $label_key = $this->getLabelKey();
    $target_bundles = $this->getTargetBundles();
    $target_bundle_key = $this->getTargetBundleKey();
    $query = \Drupal::entityQuery($entity_type_id)->condition($label_key, $value);
    $query->accessCheck(FALSE);
    if ($target_bundles && $target_bundle_key) {
      $query->condition($target_bundle_key, $target_bundles, 'IN');
    }
    if ($entities = $query->execute()) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Creates missing references.
   *
   * @param $field
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

    switch ($entity_type_id) {
      case 'taxonomy_term':
        $vocab = Vocabulary::load($target_bundle);
        $entity->vocabulary_machine_name = $vocab->id();
        var_dump($entity);
        $this->getEntityManager()->createEntity($entity_type_id, $entity->vocabulary_machine_name, $entity);
        break;

      default:
        $this->getEntityManager()->createEntity($entity_type_id, $target_bundle, $entity);
    }
  }

  /**
   * Get label key.
   *
   * @return string
   */
  protected function getLabelKey() {
    $entity_type_id = $this->getEntityTypeId();
    $entity_definition = \Drupal::entityManager()->getDefinition($entity_type_id);

    // Determine label field key.
    if ($entity_type_id !== 'user') {
      $label_key = $entity_definition->getKey('label');
    }
    else {
      // Entity Definition->getKey('label') returns false for users.
      $label_key = 'name';
    }

    return $label_key;
  }

  /**
   * Get entity type ID.
   * @return string
   */
  protected function getEntityTypeId() {
    return $this->getFieldHandler()->getFieldInfo()->getSetting('target_type');
  }

  /**
   * Get target bundle.
   *
   * @return string
   */
  protected function getTargetBundle() {
    $target_bundle = NULL;
    if ($target_bundles = $this->getTargetBundles()) {
      $target_bundle = reset($target_bundles);
    }
    return $target_bundle;
  }

  /**
   * Retrieves bundles for which the field is configured to reference.
   *
   * @return mixed
   *   Array of bundle names, or NULL if not able to determine bundles.
   */
  protected function getTargetBundles() {
    $settings = $this->getFieldHandler()->getFieldConfig()->getSettings();
    if (!empty($settings['handler_settings']['target_bundles'])) {
      return $settings['handler_settings']['target_bundles'];
    }
  }

  /**
   * Get target bundle key.
   *
   * @return null|string
   */
  protected function getTargetBundleKey() {
    $entity_definition = \Drupal::entityManager()->getDefinition($this->getEntityTypeId());
    // Determine target bundle restrictions.
    $target_bundle_key = NULL;
    if ($target_bundles = $this->getTargetBundles()) {
      $target_bundle_key = $entity_definition->getKey('bundle');
    }
    return $target_bundle_key;
  }
}
