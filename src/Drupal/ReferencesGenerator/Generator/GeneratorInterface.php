<?php

namespace Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalExtension\Context\DrupalContext;

interface GeneratorInterface {

  public function expand($value);

  public function setDrupalContext(DrupalContext $drupalContext);

  public function referenceExists($value);
}
