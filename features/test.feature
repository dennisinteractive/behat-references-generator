@test @api
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using one line
    Given a default image media
    Given I am viewing default "article" content:
      | title                  | Custom title                       |
      | field_teaser_image     | image3.jpg                         |
      | field_tags             | TagA, TagB, TagC                   |
    Then I should see "Custom title"
    And I should see "TagA"
