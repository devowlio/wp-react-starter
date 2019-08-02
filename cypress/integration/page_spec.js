const pkg = require("../../package.json");

beforeEach("Automatically login to WordPress dashboard as admin user", function() {
    //cy.exec("yarn db-snapshot-import"); // You can also reset the database before each test
    cy.exec(`yarn --silent wp-cli "wp core version"`); // Just to check if WP is generelly available
    cy.visit("/wp-login.php?autologin=wordpress");
    cy.url().should("contain", "wp-admin");
});

describe("WP Admin Page", function() {
    const componentId = pkg.name + "-component";

    it("Add and remove todo item", function() {
        const todoContainer = () => cy.get("#" + componentId + " > div > div.wp-styleguide--buttons");
        cy.get("#toplevel_page_" + componentId + " > a").click();
        todoContainer()
            .children("input")
            .type("Test Todo Item")
            .next()
            .click();
        todoContainer()
            .find("> ul > li > label:first")
            .should("have.text", "Test Todo Item")
            .children(":checkbox")
            .check()
            .should("be.checked")
            .parent()
            .parent()
            .find("a")
            .click();
        todoContainer()
            .find("> ul > li")
            .should("have.text", "No entries");
    });

    it("Test REST API response when clicking URL", function(done) {
        cy.get("#toplevel_page_" + componentId + " > a").click();
        cy.on("window:alert", (text) => {
            expect(text).to.contain("AuthorURI");
            done();
        });
        cy.get("#" + componentId + " > div > div:nth-child(3) > p > a").click();
    });
});
