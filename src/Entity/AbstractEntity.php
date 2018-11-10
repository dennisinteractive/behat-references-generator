<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Abstract Entity.
 */
abstract class AbstractEntity implements EntityInterface {
  /**
   * @var \stdClass
   */
  protected $data;

  /**
   * @var string
   */
  protected $type;

  /**
   * @var string
   */
  protected $bundle;

  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager
   */
  private $entityManager;

  /**
   * AbstractEntity constructor.
   *
   * @param $data
   * @param \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager $generatorManager
   */
  public function __construct($type, $bundle, $data, EntityManager $entityManager) {
    $this->type = $type;
    $this->bundle = $bundle;
    $this->entityManager = $entityManager;

    // Merge data with default data.
    $data = empty($data) ? [] : $data;
//var_dump(__FUNCTION__);
//var_dump($this->getDefaultContent());
    $this->data = (object) array_merge($this->getDefaultContent(), (array) $data);
    $this->data->entityType = $this->type;
    $this->data->bundle = $this->bundle;
//var_dump($this->data);

    // Parse fields into entity structure.
    $this->parseEntityFields();
    $this->generateReferences();
  }

  /**
   * Generate references.
   */
  protected function generateReferences() {
    foreach ($this->data as $field_name => $field_values) {
      if (empty($field_name)) {
        continue;
      }

      if (!is_array($field_values)) {
        $field_values = array($field_values);
      }

      foreach ($field_values as $key => $field_value) {
        if ($generator = $this->getReferenceGenerator($this->data, $field_name)) {
          if (!$generator->referenceExists($field_value)) {
            $generator->create($field_value);
          }
        }
      }
    }
  }

  /**
   * Get reference generator.
   *
   * @param $entity
   * @param $field_name
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorInterface
   * @throws \Exception
   */
  protected function getReferenceGenerator($entity, $field_name) {
    return $this->getEntityManager()->getGeneratorManager()->getReferenceGenerator($entity, $field_name);
  }

  /**
   * Use method in RawDrupalContext to convert raw data to entity object.
   *
   * @throws \Exception
   */
  protected function parseEntityFields() {
    $drupalContext = new RawDrupalContext();
    $drupalContext->setDrupal($this->getDrupal());
    $drupalContext->parseEntityFields($this->type, $this->data);
  }

  /**
   * Get default content for this entity.
   *
   * @return mixed
   */
  protected function getDefaultContent() {
    $default_content = $this->getEntityManager()->getGeneratorManager()->getDefaultContent();
    return $default_content->getContent($this->type, $this->bundle);
  }

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\EntityManager
   */
  protected function getEntityManager() {
    return $this->entityManager;
  }

  /**
   * @return \Drupal\DrupalDriverManager
   */
  protected function getDrupal() {
    return $this->getEntityManager()->getDrupal();
  }
}
