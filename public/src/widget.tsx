import React from "react";
import ReactDOM from "react-dom";
import { Widget } from "./widget/";

// Query DOM for all widget wrapper divs
const widgets = document.querySelectorAll("div.react-demo-wrapper");

// Iterate over the DOM nodes and render a React component into each node
widgets.forEach((item) => ReactDOM.render(<Widget />, item));
