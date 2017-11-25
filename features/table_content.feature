@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using table
    Given "article" content:
      | title           | body      |
      | Testing content | TEST BODY |

    Given I am on "/"
    And I should see "Testing content"
    And I should see "TEST BODY"

  @horizontal_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | title |
      | Art1  |

    Given "test" content:
      | title           | body      | field_related_articles |
      | Testing content | TEST BODY | Art1                   |

    Given I am on "/"
    And I should see "Testing content"
    And I should see "TEST BODY"
    Then I should see the link "Art1"

  @vertical_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | title |
      | Art1  |

    Given I am viewing a "test" content:
      | title                  | Testing content |
      | body                   | TEST BODY       |
      | field_related_articles | Art1            |

    Given I am on "/"
    And I should see "Testing content"
    And I should see "TEST BODY"
    Then I should see the link "Art1"
