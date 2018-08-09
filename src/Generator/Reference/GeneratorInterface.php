<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Reference;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Context\ReferencesGeneratorContext;

interface GeneratorInterface {

  public function expand($value);

  public function setReferencesContext(ReferencesGeneratorContext $referencesGeneratorContext);

  public function referenceExists($value);

  public function create($values);
}
