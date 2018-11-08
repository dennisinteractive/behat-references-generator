@api @table @behat_references_generator
Feature: Default References Generator
  In order to test the website
  as a user,
  I want to automatically generate content dependencies

  @default_content @one_line
  Scenario: Create content using one line
    Given a default "test" content

    # All values come from behat.yml
    Then I am on "test-title-yml"
    Then the response status code should be 200
    And I should see "Test title from yml"
    And I should see "Test body from yml"

    Then I should see "Test Related Articles"
    And I should see the link "Art 1 from yml"
    And I should see the link "Art 2 from yml"

    Then I should see "Test Tags"
    And I should see "Test Tag 1 from yml" in the "a[href*='test-tag-1-yml']" element
    And I should see "Test Tag 2 from yml" in the "a[href*='test-tag-2-yml']" element

    Then I should see "Test Image"
    And I should see an "img[src*='Image_from_yml.jpg']" element

    #@todo Then I should see "Test Media"

  @default_content @horizontal_table
  Scenario: Create content using tables and non-existing references, overriding values
    Given a default "test" content:
      | title                | field_test_related_articles | field_test_tags |
      | Test title overriden | New article                 | Tag 1, Tag 2    |

    Given I am on "test-title-overriden"
    Then the response status code should be 200
    And I should see "Test title overriden"
    And I should see "Test body from yml"

    Then I should see "Test Related Articles"
    And I should see the link "New article"

    Then I should see "Test Tags"
    And I should see "Tag 1" in the "a[href*='tag-1']" element
    And I should see "Tag 2" in the "a[href*='tag-2']" element

    Then I should see "Test Image"
    And I should see an "img[src*='Image_from_yml.jpg']" element

  @default_content @vertical_table
  Scenario: Create content using table and non-existing references
    Given I am viewing a default "test" content:
      | title                       | Custom title        |
      | field_test_body             | Custom Body         |
      | field_test_other_articles   | Art 1, Art 2        |
      | field_test_related_articles | Art 3, Art 4        |
      | field_test_image            | image3.jpg          |
      | field_test_tags             | Tag 1, Tag 2, Tag 3 |

    And I should see "Custom Title"
    And I should see "Custom Body"
    And I should see "Image"
    And I should see an "img[src*='image3.jpg']" element

    Then I should see "Test Tags"
    And I should see "Tag 1" in the "a[href*='tag-1']" element
    And I should see "Tag 2" in the "a[href*='tag-2']" element
    And I should see "Tag 3" in the "a[href*='tag-3']" element

    Then I should see "Test Other Articles"
    And I should see "Art 1"
    And I should see "Art 2"

    Then I should see "Test Related Articles"
    And I should see "Art 3"
    And I should see "Art 4"

    And the file "image3.jpg" should be available
