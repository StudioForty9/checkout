@custom @simple-checkout @javascript
Feature: Simple Checkout

  Background:
    Given the Simple Checkout is Enabled
    When I add a product to the cart
    And I go to the checkout

  Scenario: Checkout as Guest
    When I enter an non-registered email address
    And I fill in my Billing Address
    And I use my Billing Address as my Shipping Address
    And I choose a Shipping Method
    And I choose Payment Method
    And I save the Payment Method
    And I press "Place Order"
    Then I should be on the Success Page

  Scenario: Register on Success Page
    When I enter an non-registered email address
    And I fill in my Billing Address
    And I use my Billing Address as my Shipping Address
    And I choose a Shipping Method
    And I choose Payment Method
    And I save the Payment Method
    And I press "Place Order"
    Then I should be on the Success Page
    And I fill in "password" with "password123"
    And I press "Create Account"
    Then I am on the homepage
    
  Scenario: Login to Checkout
    Given I am a registered customer
    When I enter an registered email address
    And I fill in "login-password" with "password"
    And I press "Login"
    And I fill in my Billing Address
    And I use my Billing Address as my Shipping Address
    And I choose a Shipping Method
    And I choose Payment Method
    And I save the Payment Method
    And I press "Place Order"
    Then I should be on the Success Page
