/* istanbul ignore file: we do not need to care about the entry point file as errors are detected through integration tests (E2E) */

/**
 * The entry point for the admin side wp-admin resource.
 */
import "@wp-reactjs-multi-starter/utils"; // Import once for startup polyfilling (e. g. setimmediate)
import { render } from "react-dom";
import { ComponentLibrary } from "./components";
import { StoreProvider, rootStore } from "./store";
import "./style/admin.scss";

const node = document.getElementById(`${rootStore.optionStore.slug}-component`);

if (node) {
    render(
        <StoreProvider>
            <ComponentLibrary />
        </StoreProvider>,
        node
    );
}

// Expose this functionalities to add-ons, but you need to activate the library functionality
// in your webpack configuration, see also https://webpack.js.org/guides/author-libraries/
export * from "@wp-reactjs-multi-starter/utils";
export * from "./wp-api";
export * from "./store";
