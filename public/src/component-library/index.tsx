import React from "react";
import { Notice, ENoticeType } from "../wp";
import { Todo } from "./Todo";
import { TodoStore } from "../store";
import { Provider } from "mobx-react";
import { pluginOptions, ajax, urlBuilder } from "../util";
import { _i, __ } from "../util/i18n";
import "./style.scss";
import { locationRestPluginGet, IRequestRoutePluginGet, IParamsRoutePluginGet, IResponseRoutePluginGet } from "../rest";

// Craete default store
const todoStore = TodoStore.create();

// Do a test ajax call when clicking the REST API url
async function doTestAjaxCall(e: React.MouseEvent<HTMLAnchorElement, MouseEvent>) {
    e.persist();
    const result = await ajax<IRequestRoutePluginGet, IParamsRoutePluginGet, IResponseRoutePluginGet>({
            location: locationRestPluginGet
        }),
        usedUrl = urlBuilder({ location: locationRestPluginGet });
    alert(usedUrl + "\n\n" + JSON.stringify(result, undefined, 4) + "\n\n" + "Name: " + result.Name);
    e.preventDefault();
}

const ComponentLibrary: React.FunctionComponent<{}> = () => {
    return (
        <div className="wp-styleguide">
            <h1>WP React Component Library Overview</h1>

            <Notice type={ENoticeType.Info}>
                {__("The text domain of the plugin is: %(textDomain)s (localized variable)", {
                    textDomain: pluginOptions.textDomain
                })}
            </Notice>
            <Notice type={ENoticeType.Info}>
                {_i(
                    __("The WP REST API URL of the plugin is: {{a}}%(restUrl)s{{/a}} (localized variable)", {
                        restUrl: pluginOptions.restUrl
                    }),
                    {
                        a: (
                            <a href="#" onClick={doTestAjaxCall}>
                                {pluginOptions.restUrl}
                            </a>
                        )
                    }
                )}
            </Notice>
            <Notice type={ENoticeType.Info}>{__("The is an informative notice")}</Notice>
            <Notice type={ENoticeType.Success}>{__("Your action was successful")}</Notice>
            <Notice type={ENoticeType.Error}>{__("An unexpected error has occurred")}</Notice>

            {/* @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject */}
            <Provider store={todoStore}>
                <Todo />
            </Provider>
        </div>
    );
};

export { ComponentLibrary };
