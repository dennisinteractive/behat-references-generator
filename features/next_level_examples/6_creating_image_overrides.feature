@api @presentation @presentation_example_2
Feature: Image
  In order to make creation of images easier
  as a user,
  I want to generate an image with table node overrides

  @default_content
  Scenario: Create content using one line
    Given I have a default image:
      | filename   | Image Alt     |
      | image1.jpg | New image     |
      | image2.jpg | Another image |
