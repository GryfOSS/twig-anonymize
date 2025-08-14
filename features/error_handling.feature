Feature: Error Handling and Edge Cases
  As a developer using the Twig anonymize filter
  I want proper error handling for invalid inputs
  So that my templates fail gracefully with clear error messages

  Background:
    Given I clear all template variables
    And I clear the rendered output

  Scenario: Invalid replacement character - empty string
    Given I have template variable "text" with value "test"
    And I have a Twig template "invalid_empty_char" with content:
      """
      {{ text|anonymize(true, '') }}
      """
    When I render the template "invalid_empty_char"
    Then an exception should be thrown with message "Replacement char must be exactly 1 character long string"

  Scenario: Invalid replacement character - multiple characters
    Given I have template variable "text" with value "test"
    And I have a Twig template "invalid_multi_char" with content:
      """
      {{ text|anonymize(true, '**') }}
      """
    When I render the template "invalid_multi_char"
    Then an exception should be thrown with message "Replacement char must be exactly 1 character long string"

  Scenario: Invalid replacement character - multiple Unicode characters
    Given I have template variable "text" with value "test"
    And I have a Twig template "invalid_unicode_multi" with content:
      """
      {{ text|anonymize(true, 'ñó') }}
      """
    When I render the template "invalid_unicode_multi"
    Then an exception should be thrown with message "Replacement char must be exactly 1 character long string"

  Scenario: Valid single Unicode character replacement
    Given I have template variable "text" with value "test"
    And I have a Twig template "valid_unicode_char" with content:
      """
      {{ text|anonymize(true, 'ñ') }}
      """
    When I render the template "valid_unicode_char"
    Then no exception should be thrown
    And the output should contain "tññt"

  Scenario: Edge case - whitespace only strings
    Given I have template variables:
      | space1 | " "  |
      | space2 | "  " |
      | space3 | "   " |
    And I have a Twig template "whitespace_edge" with content:
      """
      One: "{{ space1|anonymize }}"
      Two: "{{ space2|anonymize }}"
      Three: "{{ space3|anonymize }}"
      """
    When I render the template "whitespace_edge"
    Then the output should contain 'One: "*"'
    And the output should contain 'Two: "**"'
    And the output should contain 'Three: " **"'

  Scenario: Edge case - newlines and tabs in strings
    Given I have template variable "text" with value "a b"
    And I have a Twig template "special_whitespace" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "special_whitespace"
    Then the output should contain "a**"

  Scenario: Chaining with other Twig filters
    Given I have template variable "text" with value "HELLO"
    And I have a Twig template "filter_chain" with content:
      """
      {{ text|lower|anonymize }}
      """
    When I render the template "filter_chain"
    Then no exception should be thrown
    And the output should contain "h***o"

  Scenario: Using anonymize filter in complex template structures
    Given I have template variables:
      | users | ["alice", "bob", "charlie"] |
    And I have a Twig template "complex_structure" with content:
      """
      Users:
      {% for user in users %}
      - {{ user|anonymize }}
      {% endfor %}
      """
    When I render the template "complex_structure"
    Then no exception should be thrown
    And the output should contain "- a***e"
    And the output should contain "- b**"
    And the output should contain "- c*****e"
