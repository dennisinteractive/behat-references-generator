<?php

namespace Drupal\Driver\Fields\Drupal8;

/**
 * Image field handler for Drupal 8.
 */
class ImageHandler extends AbstractHandler {

  /**
   * {@inheritdoc}
   */
  public function expand($values) {
    $return = array();
    $entity_type_id = $this->fieldInfo->getSetting('target_type');
    $entity_definition = \Drupal::entityManager()->getDefinition($entity_type_id);

    // Determine label field key.
    $label_key = $entity_definition->getKey('label');

    foreach ($values as $value) {
      $query = \Drupal::entityQuery($entity_type_id)->condition($label_key, $value);
      $query->accessCheck(FALSE);

      if ($entities = $query->execute()) {
        $return[] = array_shift($entities);
      }
      else {
        throw new \Exception(sprintf("No entity '%s' of type '%s' exists.", $value, $entity_type_id));
      }
    }

    return $return;
  }
}
