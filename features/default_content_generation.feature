@api @table
Feature: Table
  In order to use tables to create content
  as a user,
  I want to use Table tests

  Background:
    Given I have an image
    Then the file "bddtest.jpg" should be available

    Given I have an image:
      | filename   | Image Alt     |
      | image1.jpg | New image     |
      | image2.jpg | Another image |
    Then the file "image1.jpg" should be available
    Then the file "image2.jpg" should be available

  @horizontal_table @default_content @aaa
  Scenario: Create content using table and non-existing references
    Given a default "article" content
    Given a default "page" content
    Given a default "test" content
#    Given a default "test" content:
#      | Title           |
#      | Testing content |
#    Then I stop

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | Related articles | Tags |
      | Custom Article 5 | Tag5 |

    Given I am on "/"
    And I should see "Test page body"
    Then I should see the link "Custom Article 5"
    When I click "Test page title"
    Then I should see "Test page title"
    Then I should see the link "Custom Article 5"
    Then I should see the link "Tag5"

  @horizontal_table @reference_generator
  Scenario: Create content using table and non-existing references
    Given a default "test" content:
      | Title        | Body        | Related articles |
      | Custom Title | Custom Body | Art1             |

    Given I am on "/"
    And I should see "Custom Title"
    And I should see "Custom Body"
    Then I should see the link "Art1"

  @vertical_table @reference_generator @default_content @aaa
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | Title            | Custom Title                       |
      | Body             | Custom Body                        |
      | Other Articles   | Art1, Art2                         |
      | Related articles | Art3, Art4                         |
      | Primary Image    | image3.jpg                         |
      | Gallery Files    | gi1.jpg, gi2.jpg, gi3.jpg, gi4.jpg |
      | Tags             | tagA, TagB, TagC                   |

    Then I stop

    And I should see "Custom Title"
    And I should see "Primary image"
    And I should see "Custom Body"

    Then I should see "Tags:"
    And I should see the link "BDD Tag1"
    And I should see the link "BDD Tag2"
    And I should see the link "BDD Tag3"

    Then I should see "Other articles:"
    And I should see "Art1"
    And I should see "Art2"

    Then I should see "Related articles:"
    And I should see "Art3"
    And I should see "Art4"

    Then the file "image3.jpg" should be available
    And the file "gi1.jpg" should be available
    And the file "gi2.jpg" should be available
    And the file "gi3.jpg" should be available
    And the file "gi4.jpg" should be available
