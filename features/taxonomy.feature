@api @taxonomy @behat_references_generator
Feature: Default References Generator
  In order to test the website
  as a user,
  I want to automatically generate content dependencies

  @default_content
  Scenario: Create content using one line
    # Term created using default content
    Given a default "tags" term
    Given I am on "behat-test-term"
    Then the response status code should be 200
    And I should see "Behat Test Term"

    # Term created with behat.yml overrides
    Given a default "test_tags" term
    Given I am on "tag-yml"
    Then the response status code should not be 200
    And I should see "Tag from yml"
    And I should see an "img[src*='Tag_image.jpg']" element

  @default_content @horizontal_table
  Scenario: Create content using tables and non-existing references, overriding the name
    Given a default "test_tags" term:
      | name          | description     | field_test_tag_image |
      | New term name | New description | New_image.jpg        |
    Given I am on "new-term-name"
    Then the response status code should be 200
    And I should see "New term name"
    And I should see "New description"
    And I should see an "img[src*='New_image.jpg']" element

  @default_content @vertical_table
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test_tags" term:
      | name        | New term name    |
      | description | CNew description |
    And I should see "New term name"
    And I should see "New description"
    And I should see an "img[src*='Tag_image.jpg']" element
