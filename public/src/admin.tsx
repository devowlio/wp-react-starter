/**
 * The entry point for the admin side wp-admin resource.
 */

import React from "react";
import ReactDOM from "react-dom";
import { ComponentLibrary } from "./component-library";
import "setimmediate"; // Polyfill for yielding
import { pluginOptions } from "./util";

const node = document.getElementById(pluginOptions.slug + "-component");

if (node) {
    ReactDOM.render(<ComponentLibrary />, node);
}
