@api @table @behat_references_generator
Feature: Default References Generator
  In order to use tables to create content
  as a user,
  I want to use Table tests

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "test" content
    Then I am on "/"
    When I click "Test title from yml"
    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Test Tags"
    And I should see the link "Tag1"
    And I should see the link "Tag2"

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "test" content
    Then I am on "/"
    When I click "Test title from yml"

    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Test Tags"
    And I should see the link "Tag1"
    And I should see the link "Tag2"

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | field_test_related_articles | field_test_tags |
      | Custom Article 5            | Tag 5           |

    Given I am on "/"
    Then I should see the link "Custom Article 5"
    When I click "Test title from yml"
    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Test Related Articles"
    Then I should see the link "Custom Article 5"

    Then I should see "Test Tags"
    Then I should see the link "Tag 5"

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | title        | body        | field_test_related_articles |
      | Custom Title | Custom Body | Art 1                       |

    Given I am on "/"
    When I click "Custom Title"
    And I should see "Custom Title"
    And I should see "Custom Body"

    Then I should see "Test Related Articles"
    Then I should see the link "Art 1"

    Then I should see "Test Tags"
    Then I should see the link "Tag1"
    Then I should see the link "Tag2"

  @vertical_table @reference_generator @default_content
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | title                       | Custom title                       |
      | body                        | Custom Body                        |
      | field_test_other_articles   | Art1, Art2                         |
      | field_test_related_articles | Art3, Art4                         |
      | field_test_image            | image3.jpg                         |
      | field_test_tags             | TagA, TagB, TagC                   |

    And I should see "Custom Title"
    And I should see "Image"
    And I should see "Custom Body"

    Then I should see "Test Tags"
    And I should see the link "TagA"
    And I should see the link "TagB"
    And I should see the link "TagC"

    Then I should see "Test Other Articles"
    And I should see "Art1"
    And I should see "Art2"

    Then I should see "Test Related Articles"
    And I should see "Art3"
    And I should see "Art4"

    Then the file "image3.jpg" should be available
