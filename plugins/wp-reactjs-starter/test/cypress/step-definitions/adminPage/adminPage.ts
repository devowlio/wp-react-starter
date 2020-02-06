import { Then } from "cypress-cucumber-preprocessor/steps";
import { AdminPageObject } from "./AdminPageObject";

Then("I open admin page", () => {
    AdminPageObject.menuPageLink.click();
});

Then("I click on REST API link in the admin notice and alert contains {string}", (alertText: string) => {
    AdminPageObject.restApiLink.click().then(
        () =>
            new Promise((resolve) =>
                cy.on("window:alert", (text) => {
                    expect(text).to.contain(alertText);
                    resolve();
                })
            )
    );
});

Then("I type {string} and add todo, check it, afterwards delete it", (todo: string) => {
    AdminPageObject.todoInput.type(todo);
    AdminPageObject.todoAddButton.click();
    AdminPageObject.todoItem(0)
        .should("contain.text", "Test Todo Item")
        .find(":checkbox")
        .check()
        .should("be.checked");
    AdminPageObject.todoItemRemoveLink(0).click();
    AdminPageObject.todoItem(0).should("have.text", "No entries");
});
