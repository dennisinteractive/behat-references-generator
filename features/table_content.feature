@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using table
    Given "article" content:
      | Title                   | Body      |
      | Testing article content | TEST BODY |

    Given I am on "/"
    And I should see "Testing article content"
    And I should see "TEST BODY"

  @horizontal_table @reference_generator
  Scenario: Create content using table and existing references
    Given "article" content:
      | Title |
      | Art1  |

    Given "test" content:
      | Title                | Body      | Related articles |
      | Testing test content | TEST BODY | Art1             |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "TEST BODY"
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
      | Body             | TEST BODY            |
      | Related articles | Art1, Art2           |

    Given I am on "/"
    When I click "Testing test content"
    And I should see "TEST BODY"
    And I should see "Related articles"
    Then I should see the link "Art1"
    Then I should see the link "Art2"
