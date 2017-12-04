@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Background:
    Given I have an image
    Then the file "bddtest.jpg" should be available

    Given I have an image:
      | filename   | text          |
      | image1.jpg | New image     |
      | image2.jpg | Another image |
    Then the file "image1.jpg" should be available
    Then the file "image2.jpg" should be available

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | title           | body      | field_related_articles |
      | Testing content | TEST BODY | Art1                   |

    Given I am on "/"
    And I should see "Testing content"
    And I should see "TEST BODY"
    Then I should see the link "Art1"

  @vertical_table @reference_generator @default_content
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | title                  | Testing content                    |
      | body                   | TEST BODY                          |
      | field_other_articles   | Art1, Art2                         |
      | field_related_articles | Art3, Art4                         |
      | field_primary_image    | image3.jpg                         |
      | field_gallery_files    | gi1.jpg, gi2.jpg, gi3.jpg, gi4.jpg |

    And I should see "Testing content"
    And I should see "TEST BODY"

    Then I should see "Tags:"
    And I should see the link "BDD Tag1"
    And I should see the link "BDD Tag2"
    And I should see the link "BDD Tag3"

    Then I should see "Other articles:"
    And I should see "Art1"
    And I should see "Art2"

    Then I should see "Related articles:"
    And I should see "Art3"
    And I should see "Art4"

    Then the file "image3.jpg" should be available
    And the file "gi1.jpg" should be available
    And the file "gi2.jpg" should be available
    And the file "gi3.jpg" should be available
    And the file "gi4.jpg" should be available
