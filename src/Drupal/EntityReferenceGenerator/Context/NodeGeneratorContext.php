<?php

namespace Drupal\EntityReferenceGenerator\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\EntityReferenceGenerator\Content\DefaultContent;

trait NodeGeneratorContext {

  /**
   * @Given a default :type content:
   */
  public function aDefaultContent($type, TableNode $table)
  {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->drupalContext->createNodes($type, $table);
  }

  /**
   * @Given I am viewing a default :type content:
   */
  public function viewDefaultContent($type, TableNode $table) {
    if ($this->automaticallyCreateReferencedItems) {
      $this->useDefaultContent = TRUE;
    }
    $this->drupalContext->assertViewingNode($type, $table);
  }

}
