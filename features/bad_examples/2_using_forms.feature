Feature: Vehicle Review Feeds
  In order to send content to GForces
  as a PM
  I want to see a content feed.

  @api @feeds @vehicle_reviews @29373
  Scenario: Filling in forms is not very efficient
    Given I am logged in as a user with the "editor" role
    Given I am on "/node/add/review"
    Then I fill in "edit-title" with "Motors feed test"
    Then I fill in "Short Title" with "Short title text"
    Then I fill in "edit-field-primary-car-reference-und-0-taxonomy-term" with "Ford Focus hatchback"
    Then I fill in "field_review_for[und][0][value]" with "good points"
    Then I fill in "field_review_against[und][0][value]" with "bad points"
    Then I fill in "field_short_teaser[und][0][value]" with "verdict text"
    Then I fill in "field_our_choice[und][0][value]" with "our choice text"
    Then I fill in "edit-field-review-score-overall-und-0-rating" with "5"
    Then I select "2008" from "field_canonical_year_release[und][0][value][year]"
    And I select "Other" from "edit-field-content-main-purpose-und"
    And I select "Social/referral" from "edit-field-expected-traffic-source-und"
    Then I fill in "edit-field-running-costs-rating-und-0-rating" with "3"
    Then I fill in "edit-field-running-costs-description-und-0-value" with "MPG summary"
    Then I fill in "edit-field-driving-rating-und-0-rating" with "5"
    Then I fill in "edit-field-driving-description-und-0-value" with "Engines summary"
    Then I fill in "edit-field-styling-rating-und-0-rating" with "2"
    Then I fill in "edit-field-styling-description-und-0-value" with "Interior summary"
    Then I fill in "edit-field-practicality-rating-und-0-rating" with "1"
    Then I fill in "edit-field-practicality-description-und-0-value" with "Practicality summary"
    Then I fill in "edit-field-reliability-rating-und-0-rating" with "4"
    Then I fill in "edit-field-reliability-description-und-0-value" with "Reliability summary"
    And I select "Other" from "edit-field-content-main-purpose-und"
    And I select "Social/referral" from "edit-field-expected-traffic-source-und"
    Then I fill in "Associated Advanced Gallery" with "New Renault Grand Scenic 2016 review - pictures [nid:96984]"
