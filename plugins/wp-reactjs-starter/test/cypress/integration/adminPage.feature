Feature: Admin page

    Test all functionality available in the registered WP admin page

# Temporarely deactivate this test as not supported in cypress@4
#    Scenario: Click REST API link
#        Given I am logged in WP admin dashboard
#        Then I open admin page
#        Then I click on REST API link in the admin notice and alert contains "hello"
    
    Scenario: Add and remove todo list item
        Given I am logged in WP admin dashboard
        Then I open admin page
        Then I type "Test Todo Item" and add todo, check it, afterwards delete it