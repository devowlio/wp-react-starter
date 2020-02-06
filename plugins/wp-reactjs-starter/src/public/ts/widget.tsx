/* istanbul ignore file: we do not need to care about the entry point file as errors are detected through E2E */

/**
 * The entrypoint for the WordPress frontend widget.
 */

import "@wp-reactjs-multi-starter/utils"; // Import once for startup polyfilling (e. g. setimmediate)
import { render } from "react-dom";
import { Widget } from "./widget/";
import "./style/widget.scss";

// Query DOM for all widget wrapper divs
const widgets = document.querySelectorAll("div.react-demo-wrapper");

// Iterate over the DOM nodes and render a React component into each node
widgets.forEach((item) => render(<Widget />, item));
