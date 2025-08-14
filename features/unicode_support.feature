Feature: Unicode and Special Characters Support
  As a developer working with international content
  I want the anonymize filter to work with Unicode characters
  So that I can anonymize text in different languages and with special characters

  Background:
    Given I clear all template variables
    And I clear the rendered output

  Scenario: Anonymizing Unicode characters (Cyrillic)
    Given I have template variable "text" with value "тест"
    And I have a Twig template "unicode_cyrillic" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "unicode_cyrillic"
    Then the output should contain "т**т"

  Scenario: Anonymizing Unicode characters (Accented)
    Given I have template variable "text" with value "café"
    And I have a Twig template "unicode_accented" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "unicode_accented"
    Then the output should contain "c**é"

  Scenario: Anonymizing emoji characters
    Given I have template variable "text" with value "🚀test🎉"
    And I have a Twig template "emoji_text" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "emoji_text"
    Then the output should contain "🚀****🎉"

  Scenario: Using Unicode replacement characters
    Given I have template variable "text" with value "hello"
    And I have a Twig template "unicode_replacement" with content:
      """
      {{ text|anonymize(true, '●') }}
      """
    When I render the template "unicode_replacement"
    Then the output should contain "h●●●o"

  Scenario: Anonymizing special characters and symbols
    Given I have template variable "text" with value "!@#$%"
    And I have a Twig template "special_chars" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "special_chars"
    Then the output should contain "!***%"

  Scenario: Mixed content with numbers, letters, and symbols
    Given I have template variable "text" with value "user123!@"
    And I have a Twig template "mixed_content" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "mixed_content"
    Then the output should contain "u*******@"

  Scenario: Whitespace handling
    Given I have template variable "text" with value "hello world"
    And I have a Twig template "whitespace" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "whitespace"
    Then the output should contain "h*********d"

  Scenario Outline: Unicode string length handling
    Given I have template variable "text" with value "<input>"
    And I have a Twig template "unicode_length" with content:
      """
      {{ text|anonymize }}
      """
    When I render the template "unicode_length"
    Then the output should contain "<expected>"

    Examples:
      | input    | expected |
      | ñ        | *        |
      | ñó       | **       |
      | ñóü      | ñ**      |
      | привет   | п****т   |
      | 🚀       | *        |
      | 🚀🎉     | **       |
      | 🚀🎉✨   | 🚀**     |
