<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\Reference;

use DennisDigital\Behat\Drupal\ReferencesGenerator\Context\ReferencesGeneratorContext;

interface ReferenceGeneratorInterface {

  public function expand($value);

  public function setReferencesContext(ReferencesGeneratorContext $referencesGeneratorContext);

  public function referenceExists($value);
}
