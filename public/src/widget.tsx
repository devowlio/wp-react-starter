/**
 * The entrypoint for the WordPress frontend widget.
 */

import React from "react";
import ReactDOM from "react-dom";
import { Widget } from "./widget/";
import "setimmediate"; // Polyfill for yielding

// Query DOM for all widget wrapper divs
const widgets = document.querySelectorAll("div.react-demo-wrapper");

// Iterate over the DOM nodes and render a React component into each node
widgets.forEach((item) => ReactDOM.render(<Widget />, item));
