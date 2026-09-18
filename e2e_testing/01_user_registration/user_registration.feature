Feature: User registration
  As an "applicant user" of Classified
  I want to be able to register myself with the application
  So that I can submit my background checks to gain employment

  Scenario: Mandatory fields are enforced
    Given I am an anonymous user
    When I load the "registration" page
    And I enter "Bob" into the "first_name" field
    And I enter "Smith" into the "last_name" field
    And I hit the "submit" button
    Then I get an error message
    And The error message is "Mandatory fields are missing"

  Scenario: The password and confirm password fields do not match
    Given I am an anonymous user
    When I load the "registration" page
    And I enter "Bob" into the "first_name" field
    And I enter "Smith" into the "last_name" field
    And I enter "bob@example.com" into the "email" field
    And I enter "123456" into the "password" field
    And I enter "123457" into the "confirm_password" field
    And I hit the "submit" button
    Then I get an error message
    And The error message is "Passwords do not match"
