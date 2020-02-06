import * as pkg from "../../../../package.json";

const PAGE_ID = `${pkg.slug}-component`;

class AdminPageObject {
    static get todoContainer() {
        return cy.get(`#${PAGE_ID} > div > div.wp-styleguide--buttons`);
    }

    static todoItem(eq: number) {
        return this.todoContainer.find(`> ul > li:eq(${eq})`);
    }

    static todoItemRemoveLink(eq: number) {
        return this.todoItem(eq).find("a");
    }

    static get todoInput() {
        return this.todoContainer.children("input");
    }

    static get todoAddButton() {
        return this.todoInput.next();
    }

    static get menuPageLink() {
        return cy.get(`#toplevel_page_${PAGE_ID} > a`);
    }

    static get restApiLink() {
        return cy.get(`#${PAGE_ID} > div > div:nth-child(3) > p > a`);
    }
}

export { PAGE_ID, AdminPageObject };
