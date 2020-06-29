import { Then } from "cypress-cucumber-preprocessor/steps";
import { AdminPageObject } from "./AdminPageObject";

Then("I open admin page", () => {
    AdminPageObject.menuPageLink.click();
});

Then("I click on REST API link in the admin notice and alert contains {string}", (alertText: string) => {
    const stub = cy.stub();
    cy.on("window:alert", stub);

    AdminPageObject.restApiLink
        .click()
        .wait(1000)
        .then(() => {
            expect(stub.getCall(0)).to.be.calledWith(Cypress.sinon.match(new RegExp(alertText)));
        });
});

Then("I type {string} and add todo, check it, afterwards delete it", (todo: string) => {
    AdminPageObject.todoInput.type(todo);
    AdminPageObject.todoAddButton.click();
    AdminPageObject.todoItem(0).should("contain.text", "Test Todo Item").find(":checkbox").check().should("be.checked");
    AdminPageObject.todoItemRemoveLink(0).click();
    AdminPageObject.todoItem(0).should("have.text", "No entries");
});
