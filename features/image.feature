@api @table @behat_references_generator @image
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Scenario: Create a default image
    Given a default image
    Then the file "behat_test_file_image.jpg" should be available

  Scenario: Create multiple default images
    Given a default image:
      | filename   | Image Alt     |
      | image1.jpg | New image     |
      | image2.jpg | Another image |
    Then the file "image1.jpg" should be available
    Then the file "image2.jpg" should be available
