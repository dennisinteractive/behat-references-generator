@api @presentation @presentation_example_4
Feature: Default content
  In order to make content generation easier
  as a user,
  I want to create content with table node overrides

  @default_content
  Scenario: Create content and references
    Given a default "test" content:
      | Related articles | Tags  |
      | Custom Article 5 | Tag 5 |

    #And I stop
