@api @presentation @presentation_example_5
Feature: Default content
  In order to make content generation easier
  as a user,
  I want to create content with vertical table node overrides

  @default_content
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | Title            | Custom Title                       |
      | Body             | Custom Body                        |
      | Other Articles   | Art1, Art2                         |
      | Related articles | Art3, Art4                         |
      | Primary Image    | image3.jpg                         |
      | Gallery Files    | gi1.jpg, gi2.jpg, gi3.jpg, gi4.jpg |
      | Tags             | TagA, TagB, TagC                   |

    #And I stop
