@database
Feature:
  Check all order endpoints

  Scenario: Check order
    Given I set header "user_id" with value 1
    When I send a POST request to "/create_order"
    Then the response code should be 200
    And the response should have "order_id" = 1

    When I send a POST request to "/add_item/1/1"
    Then the response code should be 200
    When I send a POST request to "/add_item/1/2"
    Then the response code should be 200

    When I send a PUT request to "/add_promo/1/1"
    Then the response code should be 200
    When I send a PUT request to "/add_promo/1/1"
    Then the response code should be 500
    When I send a PUT request to "/add_promo/1/2"
    Then the response code should be 200

    When I send a GET request to "/order/1"
    Then the response code should be 200