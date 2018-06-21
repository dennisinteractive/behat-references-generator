@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Background:
    Given a default image
    Then the file "Default image.jpg" should be available

    Given a default image:
      | filename   | Image Alt     |
      | image1.jpg | New image     |
      | image2.jpg | Another image |
    Then the file "image1.jpg" should be available
    Then the file "image2.jpg" should be available

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "article" content
    Then I am on "/"
    When I click "Article title from yml"

    Then I should see "Article title from yml"
    Then I should see "Article body from yml"

    Then I should see "Tags:"
    And I should see the link "Default Tag1"
    And I should see the link "Default Tag2"
    And I should see the link "Default Tag3"

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "page" content
    Then I am on "/"
    When I click "Default Page title"

    Then I should see "Default Page title"
    Then I should see "Default Page body"

  @horizontal_table @default_content
  Scenario: Create content using one line
    Given a default "test" content
    Then I am on "/"
    When I click "Test title from yml"

    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Tags:"
    And I should see the link "Tag1"
    And I should see the link "Tag2"

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | field_related_articles | field_tags |
      | Custom Article 5       | Tag 5      |

    Given I am on "/"
    Then I should see the link "Custom Article 5"
    When I click "Test title from yml"
    Then I should see "Test title from yml"
    Then I should see "Test body from yml"

    Then I should see "Related Articles:"
    Then I should see the link "Custom Article 5"

    Then I should see "Tags:"
    Then I should see the link "Tag 5"

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | title        | body        | field_related_articles |
      | Custom Title | Custom Body | Art 1                  |

    Given I am on "/"
    When I click "Custom Title"
    And I should see "Custom Title"
    And I should see "Custom Body"

    Then I should see "Related Articles:"
    Then I should see the link "Art 1"

    Then I should see "Tags:"
    Then I should see the link "Tag1"
    Then I should see the link "Tag2"

  @vertical_table @reference_generator @default_content
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | title                  | Custom title                       |
      | body                   | Custom Body                        |
      | field_other_articles   | Art1, Art2                         |
      | field_related_articles | Art3, Art4                         |
      | field_primary_image    | image3.jpg                         |
      | field_gallery_files    | gi1.jpg, gi2.jpg, gi3.jpg, gi4.jpg |
      | field_tags             | TagA, TagB, TagC                   |

    And I should see "Custom Title"
    And I should see "Primary image"
    And I should see "Custom Body"

    Then I should see "Tags:"
    And I should see the link "TagA"
    And I should see the link "TagB"
    And I should see the link "TagC"

    Then I should see "Other articles:"
    And I should see "Art1"
    And I should see "Art2"

    Then I should see "Related Articles"
    And I should see "Art3"
    And I should see "Art4"

    Then the file "image3.jpg" should be available
    And the file "gi1.jpg" should be available
    And the file "gi2.jpg" should be available
    And the file "gi3.jpg" should be available
    And the file "gi4.jpg" should be available
