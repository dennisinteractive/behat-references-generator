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

  public function short_backtrace($limit = 0) {
    $r = [];
    $t = debug_backtrace();
    array_shift($t);
    if ($limit == 0) {
      $limit = sizeof($t);
    }
    for ($i = 0; $i <= $limit; $i++) {
      if (isset($t[$i]['file'])) {
        $r[] = [
          'function ' => $t[$i]['file'] . ':' . $t[$i]['line'] . ' function ' . $t[$i]['function'] . '()',
          'args ' => $t[$i]['args'],
        ];
      }
    }

    return $r;
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
//    var_dump($type);
//    var_dump($data);
  //if ($type == 'taxonomy_vocabulary') {
    //var_dump($this->short_backtrace(0));
//  }
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
    $entity = $this->getEntity($type, $bundle, $data);
    var_dump($data);
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
