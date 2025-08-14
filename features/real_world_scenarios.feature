Feature: Real World Usage Scenarios
  As a developer building web applications
  I want to use the anonymize filter in realistic scenarios
  So that I can protect user data while maintaining application functionality

  Background:
    Given I clear all template variables
    And I clear the rendered output

  Scenario: User profile page with anonymized data
    Given I have template variables:
      | firstName | John    |
      | lastName  | Doe     |
      | email     | john.doe@example.com |
      | phone     | +1234567890 |
    And I have a Twig template "user_profile" with content:
      """
      <div class="user-profile">
        <h1>{{ firstName|anonymize }} {{ lastName|anonymize }}</h1>
        <p>Email: {{ email|anonymize }}</p>
        <p>Phone: {{ phone|anonymize }}</p>
      </div>
      """
    When I render the template "user_profile"
    Then the output should contain "J**n"
    And the output should contain "D**"
    And the output should contain "j******************m"
    And the output should contain "+*********0"

  Scenario: Admin dashboard with sensitive data
    Given I have template variables:
      | creditCard | 4532123456789012 |
      | ssn        | 123-45-6789      |
      | apiKey     | sk_test_abcdef123456 |
    And I have a Twig template "admin_dashboard" with content:
      """
      <table>
        <tr><td>Credit Card:</td><td>{{ creditCard|anonymize }}</td></tr>
        <tr><td>SSN:</td><td>{{ ssn|anonymize }}</td></tr>
        <tr><td>API Key:</td><td>{{ apiKey|anonymize(false, '#') }}</td></tr>
      </table>
      """
    When I render the template "admin_dashboard"
    Then the output should contain "4**************2"
    And the output should contain "1*********9"
    And the output should contain "s###6"

  Scenario: Log file display with anonymized IP addresses
    Given I have template variables:
      | logEntry1 | 192.168.1.100 - GET /api/users |
      | logEntry2 | 10.0.0.50 - POST /api/login    |
    And I have a Twig template "log_display" with content:
      """
      <pre>
      {{ logEntry1|anonymize }}
      {{ logEntry2|anonymize }}
      </pre>
      """
    When I render the template "log_display"
    Then the output should contain "1****************************s"
    And the output should contain "1*************************n"

  Scenario: Comment system with anonymized usernames
    Given I have a Twig template "comments" with content:
      """
      <div class="comments">
      {% for comment in comments %}
        <div class="comment">
          <strong>{{ comment.author|anonymize }}</strong>: {{ comment.text }}
        </div>
      {% endfor %}
      </div>
      """
    And I have template variable "comments" with value '[{"author": "alice123", "text": "Great post!"}, {"author": "bob", "text": "Thanks for sharing"}]'
    When I render the template "comments"
    Then the output should contain "a******3"
    And the output should contain "b**"
    And the output should contain "Great post!"
    And the output should contain "Thanks for sharing"

  Scenario: Search results with highlighted anonymized terms
    Given I have template variables:
      | searchTerm | confidential |
      | results    | ["confidential data", "confidential info", "public data"] |
    And I have a Twig template "search_results" with content:
      """
      <div>Search for: {{ searchTerm|anonymize }}</div>
      <ul>
      {% for result in results %}
        <li>{{ result|anonymize }}</li>
      {% endfor %}
      </ul>
      """
    When I render the template "search_results"
    Then the output should contain "c**********l"
    And the output should contain "c***************a"
    And the output should contain "c***************o"
    And the output should contain "p*********a"

  Scenario: Email template with partially anonymized recipient
    Given I have template variables:
      | recipientName  | Sarah Johnson    |
      | recipientEmail | sarah.j@corp.com |
      | subject        | Important Update |
    And I have a Twig template "email_template" with content:
      """
      To: {{ recipientName|anonymize }} <{{ recipientEmail|anonymize }}>
      Subject: {{ subject }}

      Dear {{ recipientName|anonymize(false) }},

      This is an important update...
      """
    When I render the template "email_template"
    Then the output should contain "S***********n"
    And the output should contain "s**************m"
    And the output should contain "Important Update"
    And the output should contain "Dear S***n"
