import React from "react";
import { Notice, ENoticeType } from "../wp";
import { Todo } from "./Todo";
import { TodoStore } from "../store";
import { Provider } from "mobx-react";
import "./style.scss";

// Craete default store
const todoStore = TodoStore.create(),
    wprjssOpts = (window as any).wprjssOpts;

// Note: window.wprjssOpts can also be registered as external in webpack.config.js.
const ComponentLibrary: React.FunctionComponent<{}> = () => {
    return (
        <div className="wp-styleguide">
            <h1>WP React Component Library</h1>

            <Notice type={ENoticeType.Info}>
                The text domain of the plugin is: "{wprjssOpts.textDomain}" (localized variable)
            </Notice>
            <Notice type={ENoticeType.Info}>
                The WP REST API URL of the plugin is:{" "}
                <a href={wprjssOpts.restUrl + "plugin"} target="_blank">
                    {wprjssOpts.restUrl}
                </a>{" "}
                (localized variable)
            </Notice>
            <Notice type={ENoticeType.Info}>The is an informative notice</Notice>
            <Notice type={ENoticeType.Success}>Your action was successful</Notice>
            <Notice type={ENoticeType.Error}>An unexpected error has occurred</Notice>

            {/* @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject */}
            <Provider store={todoStore}>
                <Todo />
            </Provider>
        </div>
    );
};

export { ComponentLibrary };
