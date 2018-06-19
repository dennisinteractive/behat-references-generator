@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using table
    Given "article" content:
      | title        | body        |
      | Custom Title | Custom Body |

    Given I am on "/"
    And I should see "Custom Title"
    And I should see "Custom Body"

  @horizontal_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | title |
      | Art1  |

    Given "test" content:
      | title                | body        | field_related_articles |
      | Testing test content | Custom Body | Art1                   |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "Custom Body"
    And I should see "Related Articles"
    Then I should see the link "Art1"

  @vertical_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | title |
      | Art1  |
      | Art2  |

    Given I am viewing a "test" content:
      | title                  | Testing test content |
      | body                   | Custom Body          |
      | field_related_articles | Art1, Art2           |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "Custom Body"
    And I should see "Related Articles"
    Then I should see the link "Art1"
    Then I should see the link "Art2"
