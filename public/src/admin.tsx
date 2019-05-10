import React from "react";
import ReactDOM from "react-dom";
import { ComponentLibrary } from "./component-library";
import "setimmediate"; // Polyfill for yielding

const node = document.getElementById("wp-react-component-library");

if (node) {
    ReactDOM.render(<ComponentLibrary />, node);
}
