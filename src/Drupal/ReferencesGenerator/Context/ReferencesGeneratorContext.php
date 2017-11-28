<?php

namespace Drupal\ReferencesGenerator\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\HookDispatcher;
use Drupal\DrupalDriverManager;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\DrupalExtension\Context\DrupalAwareInterface;
use Drupal\DrupalUserManagerInterface;
use Drupal\ReferencesGenerator\Generator\Generator;
use Drupal\ReferencesGenerator\Content\DefaultContent;

class ReferencesGeneratorContext implements DrupalAwareInterface {

  /**
   * Drupal context.
   *
   * @var Context
   */
  protected $drupalContext;

  /**
   * When set to TRUE, referenced content will be automatically created if needed.
   */
  protected $automaticallyCreateReferencedItems = TRUE;

  /**
   * @inheritDoc
   */
  public function setDrupal(DrupalDriverManager $drupal) {
    $this->drupal = $drupal;
  }

  /**
   * @inheritDoc
   */
  public function setDispatcher(HookDispatcher $dispatcher) {
  }

  /**
   * @inheritDoc
   */
  public function getDrupal() {
    return $this->drupal;
  }

  /**
   * @var DrupalDriverManager
   */
  private $drupal;

  /**
   * @inheritDoc
   */
  public function setDrupalParameters(array $parameters) {
  }

  /**
   * @inheritdoc
   */
  public function setUserManager(DrupalUserManagerInterface $userManager) {
  }

  /**
   * @inheritdoc
   */
  public function getUserManager() {
  }

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function beforeScenario(BeforeScenarioScope $scope) {
  }

  /**
   * @BeforeScenario
   *
   * @param BeforeScenarioScope $scope
   */
  public function initialize(BeforeScenarioScope $scope) {
    // Get the environment.
    $environment = $scope->getEnvironment();

    // Get all the contexts we need.
    $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');

    // Ensure drupal is bootstrapped by getting the driver.
    $this->drupalContext->getDriver('drupal');
  }

  /**
   * @AfterScenario
   *
   * @param AfterScenarioScope $scope
   */
  public function afterScenario(AfterScenarioScope $scope) {
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeNodeCreate
   */
  public function assignEntityTypeNode(EntityScope $scope) {
    $entity = $scope->getEntity();
    $entity->entityType = 'node';
  }

  /**
   * Assign the entity type to the scope.
   *
   * @beforeTermCreate
   */
  public function assignEntityTypeTerm(EntityScope $scope) {
    $entity = $scope->getEntity();
    $entity->entityType = 'term';
  }

  /**
   * Fills in default fields for known entities provided by getDefaultNode()
   * Creates referenced content if needed.
   *
   * @beforeNodeCreate
   * @beforeTermCreate
   */
  public function createNonexistingReferences(EntityScope $scope) {
    $entity = $scope->getEntity();
    if (!isset($entity->entityType)) {
      return;
    }

    if (isset($this->useDefaultContent) && $this->useDefaultContent == TRUE) {
      // Fill in default values
      $default = new DefaultContent($entity->entityType);
      $bundleName = isset($entity->type) ? $entity->type : '';
      $defaults = $default->mapping($bundleName);
      foreach ($defaults as $fieldName => $value) {
        if (!isset($entity->{$fieldName})) {
          $entity->{$fieldName} = $defaults[$fieldName];
        }
      }

      $tmpEntity = clone $entity;
      $this->drupalContext->parseEntityFields($entity->entityType, $tmpEntity);

      // Create referenced entities.
      foreach ($tmpEntity as $fieldName => $fieldValues) {

        $field = field_read_field($fieldName);
        if (empty($field)) {
          // Field doesn't exist.
          continue;
        }
        $fieldType = $field['type'];

        if (!is_array($fieldValues)) {
          $fieldValues = array($fieldValues);
        }

        foreach ($fieldValues as $key => $fieldValue) {
          //if ($generator = $this->getGenerator($entity, $fieldType, $fieldName)) {
          if ($generator = $this->getGenerator($entity, $fieldType, $fieldName)) {
            $generator->setDrupalContext($this->drupalContext);
            if (!$generator->referenceExists($fieldValue)) {
              // @todo create() should use $scope->getContext()->createNode() instead of this->drupalcontext
              $generator->create($field, $fieldValue);
            }
          }
        }
      }

      // If pathauto is enabled, set the path.
      //if (module_exists('pathauto') && isset($entity->alias)) {
      if (isset($entity->alias)) {
        // @todo there is a bug here, it sets the same path to all terms.
        $entity->path = array(
          'alias' => $entity->alias,
          'pathauto' => 0
        );
        unset($entity->alias);
      }

//      @todo do something about this
//      // Temporary fix to populate the default value of published date. This should be populated using some hook.
//      if (!isset($entity->field_published_date)) {
//        $entity->field_published_date = array(
//          'und' => array(
//            '0' => array(
//              'value' => gmDate('Y-m-d H:i:s'),
//              'timezone' => 'UTC',
//              'timezone_db' => 'UTC',
//              'date_type' => 'datetime',
//            )
//          )
//        );
//      }
      // print_r($entity); ob_flush();
    }
  }

  /**
   * Gets the generator class for the reference type.
   *
   * @param $field
   */
  private function getGenerator($entity, $fieldType, $fieldName) {
    return Generator::getGenerator($entity, $fieldType, $fieldName);
  }

  /**
   * Generates a table node from array.
   *
   * @param $table
   */
  public function getTableNode($table) {
    // Reformat array.
    $table = array_merge(
      array(
        array_keys($table)
      ),
      array(
        array_values($table)
      ));

    return new TableNode($table);
  }

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

  /**
   * @Given I have an image
   */
  public function defaultImage() {
    $default = new DefaultContent('image');
    $defaultImage = $default->mapping();
    $nodesTable = $this->getTableNode($defaultImage);

    return $this->iCreateAFile($nodesTable);

  }

  /**
   * @Given I create a file
   */
  public function iCreateAFile(TableNode $nodesTable) {
    $default = new DefaultContent('image');
    $defaultImage = $default->mapping();
    $defaultImage['text'] = 'BDD TEST';

    // Create images from the first row of table data.
    $table_items = $nodesTable->getHash()[0];

    // Change all the keys to lowercase.
    $table_items = (array_change_key_case($table_items, CASE_LOWER));

    // Allow text to be overriden.
    if (isset($table_items['text'])) {
      $defaultImage['text'] = $table_items['text'];
    }

    // Allow Credits to be overriden.
    if (isset($table_items['credits'])) {
      $defaultImage['credits'] = $table_items['credits'];
    }

    // Allow Description to be overriden.
    if (isset($table_items['description'])) {
      $defaultImage['description'] = $table_items['description'];
    }

    // Allow Alt text to be overriden.
    if (isset($table_items['file_alt_text'])) {
      $defaultImage['alt_text'] = $table_items['file_alt_text'];
    }

    // Allow filename to be overriden.
    if (isset($table_items['filename'])) {
      $defaultImage['filename'] = $table_items['filename'];
    }
    $local_filename = '/tmp/' . $defaultImage['filename'];

    // Create a blank image and add some text
    $im = imagecreatetruecolor(200, 200);
    $text_color = imagecolorallocate($im, 0, 0, 255);
    imagestring($im, 5, 60, 90, $defaultImage['text'], $text_color);

    $color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($im, 0, 0, $color);

    // Save the image as 'simpletext.jpg'
    imagejpeg($im, $local_filename);

    // Free up memory
    imagedestroy($im);

    // Create a file object.
    $dfile = (object) array(
      'uri' => $local_filename,
      'filemime' => file_get_mimetype($local_filename),
      'status' => 1,
    );

    // Copy the file.
    $public_uri = 'public://' . $defaultImage['filename'];
    $file = file_copy($dfile, $public_uri);
    $fid = $file->fid;
    $this->cleanFids[] = $fid;

    $mediafiles = entity_load('file', array($fid));

    // Change fields.
    $mediafiles[$fid]->filename = $defaultImage['filename'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['value'] = $defaultImage['credits'];
    $mediafiles[$fid]->field_file_credits[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['credits'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['format'] = 'plain_text';
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['value'] = $defaultImage['description'];
    $mediafiles[$fid]->field_file_description[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['description'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['value'] = $defaultImage['alt_text'];
    $mediafiles[$fid]->field_file_alt_text[LANGUAGE_NONE][0]['safe_value'] = $defaultImage['alt_text'];

    // Save the attributes.
    entity_save('file', $mediafiles[$fid]);

    return ($mediafiles[$fid]);
  }

  /**
   * @Then the file :image should be available
   */
  public function theFileShouldBeAvailable($image) {
    $path = file_create_url('public://' . $image);
    $this->drupalContext->getSession()->visit($path);
    if ($this->drupalContext->getSession()->getStatusCode() !== 200) {
      throw new \Exception(sprintf('Could not find image on %s', $path));
    };
  }

}
