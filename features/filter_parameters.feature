Feature: Anonymize Filter Parameters
  As a developer using the Twig anonymize filter
  I want to customize the anonymization behavior
  So that I can control the output format and replacement characters

  Background:
    Given I clear all template variables
    And I clear the rendered output

  Scenario: Using custom replacement character
    Given I have template variable "text" with value "hello"
    When I render template with anonymize filter using replacement char "#"
    Then the output should contain "h###o"

  Scenario: Using different replacement characters
    Given I have template variable "text" with value "test"
    And I have a Twig template "custom_chars" with content:
      """
      Hash: {{ text|anonymize(true, '#') }}
      Dash: {{ text|anonymize(true, '-') }}
      Dot: {{ text|anonymize(true, '.') }}
      Unicode: {{ text|anonymize(true, '●') }}
      """
    When I render the template "custom_chars"
    Then the output should contain "Hash: t##t"
    And the output should contain "Dash: t--t"
    And the output should contain "Dot: t..t"
    And the output should contain "Unicode: t●●t"

  Scenario: Using keepLength parameter set to true (default)
    Given I have template variable "text" with value "testing"
    When I render template with anonymize filter using keepLength "true"
    Then the output should contain "t*****g"
    And the output length should be 7

  Scenario: Using keepLength parameter set to false
    Given I have template variable "text" with value "verylongpassword"
    When I render template with anonymize filter using keepLength "false"
    Then the output should contain "v***d"
    And the output length should be 5

  Scenario: Combining custom replacement char and keepLength false
    Given I have template variable "text" with value "longpassword"
    And I have a Twig template "combined_params" with content:
      """
      {{ text|anonymize(false, '#') }}
      """
    When I render the template "combined_params"
    Then the output should contain "l###d"
    And the output length should be 5

  Scenario: Testing all parameter combinations
    Given I have template variable "text" with value "example"
    And I have a Twig template "all_combinations" with content:
      """
      Default: {{ text|anonymize }}
      KeepLength true: {{ text|anonymize(true) }}
      KeepLength false: {{ text|anonymize(false) }}
      Custom char: {{ text|anonymize(true, '@') }}
      Both params: {{ text|anonymize(false, '#') }}
      """
    When I render the template "all_combinations"
    Then the output should contain "Default: e*****e"
    And the output should contain "KeepLength true: e*****e"
    And the output should contain "KeepLength false: e***e"
    And the output should contain "Custom char: e@@@@@e"
    And the output should contain "Both params: e###e"
