<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Entity;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager;
use Drupal\DrupalDriverManager;

class EntityManager {
  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager
   */
  private $generatorManager;

  /**
   * @var \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Entity\EntityInterface[]
   */
  protected $entities = [];

  /**
   * @inheritdoc
   */
  public function __construct(GeneratorManager $generatorManager) {
    $this->generatorManager = $generatorManager;
  }

  /**
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager
   */
  public function getGeneratorManager() {
    return $this->generatorManager;
  }

  /**
   * @return \Drupal\DrupalDriverManager
   */
  public function getDrupal() {
    return $this->getGeneratorManager()->getDrupal();
  }

  /**
   * Gets the entity class.
   *
   * @param $type
   * @param $data
   * @return EntityInterace
   * @throws \Exception
   */
  public function getEntity($type, $bundle, $data) {
    $core = $this->getDrupal()->getDriver()->getDrupalVersion();

    $mapping = array(
      'media' => 'Media',
      'file' => 'File',
      'node' => 'Node',
      'taxonomy_term' => 'TaxonomyTerm',
    );

    if (isset($mapping[$type])) {
      $class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\Drupal%s\%s', $core, $mapping[$type]);
      $default_class_name = sprintf('\DennisDigital\Behat\Drupal\ReferencesGenerator\Entity\%s', $mapping[$type]);
      if (class_exists($class_name)) {
        return new $class_name($type, $bundle, $data, $this);
      }
      elseif (class_exists($default_class_name)) {
        return new $default_class_name($type, $bundle, $data, $this);
      }
      else {
        throw new \Exception("Cannot find $default_class_name class");
      }
    }

    throw new \Exception("Cannot find entity generator class for " . $type . ' ' . $bundle);
  }

  /**
   * Creates entity.
   *
   * @param $type
   * @param $data
   * @throws \Exception
   */
  public function createEntity($type, $bundle, $data) {

//    if ($type == 'taxonomy_vocabulary') {
//      echo 'This is only here because of a bug that needs to be investigated';
//      return;
//    }

    $entity = $this->getEntity($type, $bundle, $data);
//var_dump(__FUNCTION__);
//var_dump($data);
    $saved = $entity->save();
    $this->entities[] = $entity;
    return $saved;
  }

  /**
   * Cleanup entities.
   */
  public function cleanup() {
    foreach ($this->entities as $entity) {
      $entity->delete();
    }
  }
}
