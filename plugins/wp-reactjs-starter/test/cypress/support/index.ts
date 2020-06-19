// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import "./commands";
import "cypress-plugin-retries";

// Alternatively you can use CommonJS syntax:
// require('./commands')

// See https://docs.cypress.io/api/cypress-api/cookies.html#Whitelist-accepts
Cypress.Cookies.defaults({
    whitelist: /wordpress_/
});

// `window.fetch` does not work yet with cypress (https://git.io/JfFdL)
Cypress.on("window:before:load", (win: Window) => {
    win.fetch = null;
});
