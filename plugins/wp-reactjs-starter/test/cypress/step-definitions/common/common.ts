import { Given } from "cypress-cucumber-preprocessor/steps";

Given("I am logged in WP admin dashboard", () => {
    cy.visit("/wp-login.php?autologin=wordpress");
    cy.url().should("contain", "wp-admin");
});
