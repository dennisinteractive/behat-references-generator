@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using table
    Given "article" content:
      | Title        | Body        |
      | Custom Title | Custom Body |

    Given I am on "/"
    And I should see "Custom Title"
    And I should see "Custom Body"

  @horizontal_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | Title |
      | Art1  |

    Given "test" content:
      | Title                | Body        | Related articles |
      | Testing test content | Custom Body | Art1             |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "Custom Body"
    And I should see "Related articles"
    Then I should see the link "Art1"

  @vertical_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | Title |
      | Art1  |
      | Art2  |

    Given I am viewing a "test" content:
      | Title            | Testing test content |
      | Body             | Custom Body          |
      | Related articles | Art1, Art2           |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "Custom Body"
    And I should see "Related articles"
    Then I should see the link "Art1"
    Then I should see the link "Art2"
