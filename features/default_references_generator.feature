@api @table @behat_references_generator
Feature: Default References Generator
  In order to test the website
  as a user,
  I want to automatically generate content dependencies

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "test" content

    Then I am on "/"

    # All values come from behat.yml
    When I click "Test title from yml"

    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Test Related Articles"
    And I should see the link "Art1"
    And I should see the link "Art2"

    Then I should see "Test Tags"
    And I should see the link "Tag1"
    And I should see the link "Tag2"

  @horizontal_table @reference_generator
  Scenario: Create content using tables and non-existing references, overriding title
    Given a default "test" content:
      | title               | field_test_related_articles | field_test_tags       |
      | Test title override | New article                 | Tag1, New Tag         |

    Given I am on "/"
    Then I should see the link "New article"

    # Default title from behat.yml was overriden
    When I click "Test title override"

    # Body from yml stays the same
    Then I should see "Test body from yml"

    Then I should see "Test Related Articles"
    Then I should see the link "New article"

    Then I should see "Test Tags"
    Then I should see the link "Tag1"
    Then I should see the link "New Tag"

  @vertical_table @reference_generator @default_content
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | title                       | Custom title     |
      | body                        | Custom Body      |
      | field_test_other_articles   | Art1, Art2       |
      | field_test_related_articles | Art3, Art4       |
      | field_test_image            | image3.jpg       |
      | field_test_tags             | TagA, TagB, TagC |

    And I should see "Custom Title"
    And I should see "Custom Body"
    And I should see "Image"
    And I should see an "img[src*='image3.jpg']" element

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

    And the file "image3.jpg" should be available
