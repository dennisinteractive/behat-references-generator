@test @api
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using one line
    Given a default "article" content
    Then I am on "/"
    When I click "Article title from yml"

    Then I should see "Article title from yml"

    And I should see the link "Tag1"
    And I should see the link "Tag2"