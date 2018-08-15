<?php

namespace DennisDigital\Behat\Drupal\ReferencesGenerator\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Content\DefaultContent;
use DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager;

class ReferencesGeneratorContext extends RawDrupalContext {
  /**
   * Stores the default content mapping.
   */
  protected $defaultContentMapping;

  /**
   * @var GeneratorManager
   */
  protected $generatorManager;

  /**
   * @inheritdoc
   */
  public function __construct($parameters = array()) {
    $this->defaultContentMapping = $parameters['default_content'];
  }

  /**
   * Get Generator
   *
   * @return \DennisDigital\Behat\Drupal\ReferencesGenerator\Generator\GeneratorManager
   */
  protected function getGeneratorManager() {
    if (!isset($this->generatorManager)) {
      $default_content = new DefaultContent($this->defaultContentMapping);
      $this->generatorManager = new GeneratorManager($this->getDrupal(), $default_content);
    }
    return $this->generatorManager;
  }

  /**
   * Create an entity.
   *
   * @param $type
   * @param $data
   * @throws \Exception
   */
  protected function createEntity($type, $bundle, $data = NULL) {
    return $this->getGeneratorManager()->getEntityManager()->createEntity($type, $bundle, $data);
  }

  /**
   * Creates content of a given type provided in the form:
   * | title    | author     | status | created           |
   * | My title | Joe Editor | 1      | 2014-10-17 8:00am |
   * | ...      | ...        | ...    | ...               |
   */
  protected function createNodes($type, TableNode $nodesTable) {
    foreach ($nodesTable->getHash() as $nodeHash) {
      $node = (object) $nodeHash;
      $node->type = $type;
      $node->useDefaultContent = TRUE;
      $this->nodeCreate($node);
    }
  }

  /**
   * Create a node.
   *
   * @return object
   *   The created node.
   */
  public function nodeCreate($node) {
    $this->dispatchHooks('BeforeNodeCreateScope', $node);
    $saved = $this->createEntity('node', $node->type, $node);
    $this->dispatchHooks('AfterNodeCreateScope', $saved);
    $this->nodes[] = $saved;
    return $saved;
  }

  //-----------------------------------------------------//
  //                   Event hooks                       //
  //-----------------------------------------------------//

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function initialize(BeforeScenarioScope $scope) {
    $this->getDrupal()->getDriver('drupal');
  }

  /**
   * Delete references after scenario.
   *
   * @AfterScenario
   */
  public function removeReferences(AfterScenarioScope $scope) {
    $this->getGeneratorManager()->cleanUp();
  }

  //-----------------------------------------------------//
  //                  Step definitions                   //
  //-----------------------------------------------------//

  /**
   * @Given a default :type content
   */
  public function defaultContent($nodeType) {
    $table = TableNode::fromList(array('',''));
    $this->createNodes($nodeType, $table);
  }

  /**
   * @Given a default :type content:
   */
  public function defaultContentWithOverrides($type, TableNode $table) {
    $this->createNodes($type, $table);
  }

  /**
   * Creates content of the given type, provided in the form:
   * | title     | My node        |
   * | Field One | My field value |
   * | author    | Joe Editor     |
   * | status    | 1              |
   * | ...       | ...            |
   *
   * @Given I am viewing default :type( content):
   */
  public function viewingDefaultNode($type, TableNode $fields) {
    $node = (object) array(
      'type' => $type,
    );
    foreach ($fields->getRowsHash() as $field => $value) {
      $node->{$field} = $value;
    }

    $saved = $this->nodeCreate($node);

    // Set internal browser on the node.
    $this->getSession()->visit($this->locatePath('/node/' . $saved->nid));
  }

  /**
   * @Given a default image media
   */
  public function defaultMediaImage() {
    $image = $this->createEntity('media', 'image');
  }

  /**
   * @Given a default image file
   */
  public function defaultImageFile() {
    $image = $this->createEntity('file', 'image');
  }

}
