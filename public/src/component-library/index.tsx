import React from "react";
import { Notice, ENoticeType } from "../wp";
import { Todo } from "./Todo";
import { TodoStore } from "../store";
import { Provider } from "mobx-react";
import { pluginOptions } from "../util";
import { translate } from "../util/i18n";
import "./style.scss";

// Craete default store
const todoStore = TodoStore.create();

// Note: window.wprjssOpts can also be registered as external in webpack.config.js.
const ComponentLibrary: React.FunctionComponent<{}> = () => {
    return (
        <div className="wp-styleguide">
            <h1>WP React Component Library</h1>

            <Notice type={ENoticeType.Info}>
                {translate("textDomainNotice", { args: { textDomain: pluginOptions.textDomain } })}
            </Notice>
            <Notice type={ENoticeType.Info}>
                {translate("restUrlNotice", {
                    args: { restUrl: pluginOptions.restUrl },
                    components: {
                        a: (
                            <a href={pluginOptions.restUrl + "plugin"} target="_blank">
                                {pluginOptions.restUrl}
                            </a>
                        )
                    }
                })}
            </Notice>
            <Notice type={ENoticeType.Info}>{translate("infoNotice")}</Notice>
            <Notice type={ENoticeType.Success}>{translate("successNotice")}</Notice>
            <Notice type={ENoticeType.Error}>{translate("errorNotice")}</Notice>

            {/* @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject */}
            <Provider store={todoStore}>
                <Todo />
            </Provider>
        </div>
    );
};

export { ComponentLibrary };
