@test @api
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create content using one line
    Given a default image media
    Given I am viewing default "article" content:
      | title                  | Custom title                       |
      | field_teaser_media     | image3.jpg                         |
      | field_teaser_text      | My summary                         |
      | field_tags             | TagA, TagB, TagC                   |
      | field_channel          | News                               |
    Then I should see "Custom title"
    And I am on "news"
    Then the response should contain "image3.jpg"
