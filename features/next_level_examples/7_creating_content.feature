@api @presentation_example_3
Feature: Default content
  In order to make content generation easier
  as a user,
  I want to create content using only one line

  @default_content
  Scenario: Create an article and referenced content using one line
    Given a default "article" content

    And I stop
