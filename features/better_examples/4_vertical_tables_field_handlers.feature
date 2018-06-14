@api
Feature: FieldHandlers
  In order to prove field handling is working properly
  As a developer
  I need to use the step definitions of this context

  @d7 @d8
  Scenario: Test using human readable names for fields using @Transform
    Given "page" content:
      | title      |
      | Page one   |
      | Page two   |
      | Page three |
    When I am viewing a "post" content:
      | title     | Post title                                                                       |
      | body      | PLACEHOLDER BODY                                                                 |
      | reference | Page one, Page two                                                               |
      | date      | 2015-02-08 17:45:00                                                              |
      | links     | Link 1 - http://example.com, Link 2 - http://example.com                         |
      | select    | One, Two                                                                         |
      | address   | country: BE - locality: Brussel - thoroughfare: Louisalaan 1 - postal_code: 1000 |
    Then I should see "Page one"
    And I should see "Page two"
    And I should see "Sunday, February 8, 2015"
    And I should see the link "Link 1"
    And I should see the link "Link 2"
    And I should see "One"
