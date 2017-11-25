@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

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
      | title                  | Testing content |
      | body                   | TEST BODY       |
      | field_other_articles   | Art1, Art2      |
      | field_related_articles | Art3, Art4      |

    And I should see "Testing content"
    And I should see "TEST BODY"

    Then I should see "Tags:"
    Then I should see the link "BDD Tag1"
    Then I should see the link "BDD Tag2"
    Then I should see the link "BDD Tag3"

    Then I should see "Other articles:"
    Then I should see "Art1"
    Then I should see "Art2"

    Then I should see "Related articles:"
    Then I should see "Art3"
    Then I should see "Art4"

