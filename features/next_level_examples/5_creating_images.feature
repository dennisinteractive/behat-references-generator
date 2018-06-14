@api @presentation_example_1
Feature: Image
  In order to make creation of images easier
  as a user,
  I want to generate an image with one line

  @default_content
  Scenario: Create content using one line
    Given I have an image

    Then the file "bddtest.jpg" should be available

    And I stop
