Feature: Basic Anonymize Filter Functionality
  As a developer using the Twig anonymize filter
  I want to anonymize strings in my templates
  So that I can protect sensitive information while maintaining readability

  Background:
    Given I clear all template variables
    And I clear the rendered output

  Scenario: Anonymizing empty strings
    Given I have template variable "text" with value ""
    And I have a Twig template "empty_test" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "empty_test"
    Then the output should be exactly:
      """

      """

  Scenario: Anonymizing single character strings (length < 3)
    Given I have template variable "text" with value "a"
    And I have a Twig template "single_char" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "single_char"
    Then the output should contain "*"
    And the output length should be 1

  Scenario: Anonymizing two character strings (length < 3)
    Given I have template variable "text" with value "hi"
    And I have a Twig template "two_chars" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "two_chars"
    Then the output should contain "**"
    And the output length should be 2

  Scenario: Anonymizing three character strings (length = 3)
    Given I have template variable "text" with value "cat"
    And I have a Twig template "three_chars" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "three_chars"
    Then the output should contain "c**"
    And the output length should be 3

  Scenario: Anonymizing longer strings (length > 3)
    Given I have template variable "text" with value "hello"
    And I have a Twig template "long_string" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "long_string"
    Then the output should contain "h***o"
    And the output length should be 5

  Scenario: Anonymizing very long strings
    Given I have template variable "text" with value "verylongpassword"
    And I have a Twig template "very_long" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "very_long"
    Then the output should contain "v**************d"
    And the output length should be 16

  Scenario Outline: Testing different string lengths
    Given I have template variable "text" with value "<input>"
    And I have a Twig template "length_test" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "length_test"
    Then the output should contain "<expected>"

    Examples:
      | input     | expected    |
      | a         | *           |
      | ab        | **          |
      | abc       | a**         |
      | test      | t**t        |
      | hello     | h***o       |
      | password  | p******d    |
