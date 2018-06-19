@api @exclude
Feature: DrupalContext
  In order to prove the Drupal context is working properly
  As a developer
  I need to use the step definitions of this context

  @d7 @d8
  Scenario: Create an article with multiple term references
    Given "tags" terms:
      | name      |
      | Tag one   |
      | Tag two   |
      | Tag,three |
      | Tag four  |
    And "article" content:
      | title           | body             | promote | field_tags                    |
      | Article by Joe  | PLACEHOLDER BODY |       1 | Tag one, Tag two, "Tag,three" |
      | Article by Mike | PLACEHOLDER BODY |       1 | Tag four                      |
    When I am on the homepage
    Then I should see the link "Tag one"
    And I should see the link "Tag two"
    And I should see the link "Tag,three"
    And I should see the link "Tag four"
