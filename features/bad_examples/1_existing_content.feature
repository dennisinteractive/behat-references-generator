@presentation
Feature: Media by make blocks
  In order to surface relevant media content when on a make page
  As a User
  I want to see a video and picture block showing relevant and recent content.

  @19721 @blocks @make
  Scenario Outline: Visit existing pages is fast but can break anytime the content changes
    Given I am on "<url>"
    Then the response status code should be 200
    And I should see the heading "<title>"
    And I should see an ".view-display-id-block .views-field-field-primary-image" element
    And I should see an ".view-display-id-block .views-field-title" element

    Examples:
      | url   | title |
      | /kia  | Kia   |
      | /ford | Ford  |
      | /bmw  | BMW   |
