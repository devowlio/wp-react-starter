import $ from "jquery";
import {
    WP_REST_API_USE_GLOBAL_METHOD,
    RouteRequestInterface,
    RouteParamsInterface,
    RouteResponseInterface,
    RequestArgs,
    commonUrlBuilder,
    RouteHttpVerb
} from "./";

/**
 * Build and execute a specific REST query.
 *
 * @see urlBuilder
 * @returns Result of REST API
 * @throws
 */
async function commonRequest<
    Request extends RouteRequestInterface,
    Params extends RouteParamsInterface,
    Response extends RouteResponseInterface
>({
    location,
    options,
    request: routeRequest,
    params,
    settings = {}
}: {
    request?: Request;
    params?: Params;
    settings?: JQuery.AjaxSettings<any>;
} & RequestArgs): Promise<Response> {
    const url = commonUrlBuilder({ location, params, nonce: false, options });

    // Use global parameter (see https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/)
    if (WP_REST_API_USE_GLOBAL_METHOD && location.method && location.method !== RouteHttpVerb.GET) {
        settings.method = "POST";
    }

    const result = await $.ajax(
        $.extend(true, settings, {
            url,
            headers: {
                "X-WP-Nonce": options.restNonce
            },
            data: routeRequest
        })
    );
    return result as Response;
}

export { commonRequest };
