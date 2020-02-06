import React, { FC } from "react";
import { observer } from "mobx-react";
import { Notice, NoticeType } from "@wp-reactjs-multi-starter/utils";
import { TodoOverview } from "./todo";
import { RequestRouteHelloGet, ParamsRouteHelloGet, ResponseRouteHelloGet, locationRestHelloGet } from "../wp-api";
import { useStores } from "../store";
import { request, urlBuilder, __, _i } from "../utils";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
/**
 * Do a test ajax call when clicking the REST API url.
 *
 * @param e
 */
async function doHelloWorldRestCall(event: React.MouseEvent) {
    event.persist();
    const result = await request<RequestRouteHelloGet, ParamsRouteHelloGet, ResponseRouteHelloGet>({
        location: locationRestHelloGet
    });
    const usedUrl = urlBuilder({ location: locationRestHelloGet });
    alert(`${usedUrl}\n\n${JSON.stringify(result, undefined, 4)}`);
    event.preventDefault();
}

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
const ComponentLibrary: FC<{}> = observer(() => {
    const { optionStore } = useStores();
    return (
        <div className="wp-styleguide">
            <h1>WP React Component Library</h1>

            <Notice type={NoticeType.Info}>
                {__("The text domain of the plugin is: %(textDomain)s (localized variable)", {
                    textDomain: optionStore.textDomain
                })}
            </Notice>
            <Notice type={NoticeType.Info}>
                {_i(
                    __(
                        "The WP REST API URL of the plugin is: {{a}}%(restUrl)s{{/a}} (localized variable, click for hello world example)",
                        {
                            restUrl: optionStore.restUrl
                        }
                    ),
                    {
                        a: (
                            <a href="#" onClick={doHelloWorldRestCall}>
                                {optionStore.restUrl}
                            </a>
                        )
                    }
                )}
            </Notice>
            <Notice type={NoticeType.Info}>{__("The is an informative notice")}</Notice>
            <Notice type={NoticeType.Success}>{__("Your action was successful")}</Notice>
            <Notice type={NoticeType.Error}>{__("An unexpected error has occurred")}</Notice>
            <TodoOverview />
        </div>
    );
});

export { ComponentLibrary };
