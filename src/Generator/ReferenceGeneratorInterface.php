<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Generator;

use Drupal\DrupalExtension\Context\DrupalContext;

interface ReferenceGeneratorInterface {

  public function expand($value);

  public function setDrupalContext(DrupalContext $drupalContext);

  public function referenceExists($value);
}
