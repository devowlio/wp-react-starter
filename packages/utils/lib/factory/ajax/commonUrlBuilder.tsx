import Url from "url-parse";
import { trailingslashit, untrailingslashit } from "../../helpers";
import { BaseOptions } from "../../options";
import { RouteHttpVerb } from ".";
import deepMerge from "deepmerge";

// Use _method instead of Http verb
const WP_REST_API_USE_GLOBAL_METHOD = true;

interface RouteLocationInterface {
    path: string;
    namespace?: string;
    method?: RouteHttpVerb;
}

type RouteRequestInterface = {}; // POST-Query in JSON-format
type RouteParamsInterface = {}; // Parameters in IRouteLocationInterface#path which gets resolved like "example/:id", items not found in the URL are appended as GET
type RouteResponseInterface = {}; // Result in JSON-format

interface RequestArgs {
    location: RouteLocationInterface;
    params?: RouteParamsInterface;
    options: {
        restRoot: BaseOptions["restRoot"];
        restNamespace: BaseOptions["restNamespace"];
        restNonce?: BaseOptions["restNonce"];
        restQuery?: BaseOptions["restQuery"];
    };
}

/**
 * Build an URL for a specific scheme.
 *
 * @param param0
 */
function commonUrlBuilder({
    location,
    params = {},
    nonce = true,
    options
}: {
    nonce?: boolean;
} & RequestArgs) {
    const apiUrl = new Url(options.restRoot, true);
    const { query } = apiUrl;
    const permalinkPath = (query.rest_route as string) || apiUrl.pathname; // Determine path from permalink settings

    // Find dynamic parameters from URL bindings (like /user/:id)
    const foundParams: string[] = [];
    const path = location.path.replace(/:([A-Za-z0-9-_]+)/g, (match: string, group: string) => {
        foundParams.push(group);
        return (params as any)[group];
    });
    const getParams: any = {};

    // Find undeclared body params (which are not bind above) and add it to GET query
    for (const checkParam of Object.keys(params)) {
        if (foundParams.indexOf(checkParam) === -1) {
            getParams[checkParam] = (params as any)[checkParam]; // We do not need `encodeURIComponent` as it is supported by `url-parse` already
        }
    }

    const usePath =
        trailingslashit(permalinkPath) + untrailingslashit(location.namespace || options.restNamespace) + path;

    // Force protocol from parent location
    const useThisProtocol = new Url(window.location.href).protocol.slice(0, -1);
    apiUrl.set("protocol", useThisProtocol);

    // Set path depending on permalink settings
    if (query.rest_route) {
        query.rest_route = usePath; // eslint-disable-line @typescript-eslint/naming-convention
    } else {
        apiUrl.set("pathname", usePath); // Set path
    }

    // Append others
    if (nonce && options.restNonce) {
        query._wpnonce = options.restNonce;
    }
    if (WP_REST_API_USE_GLOBAL_METHOD && location.method && location.method !== RouteHttpVerb.GET) {
        query._method = location.method;
    }

    return apiUrl.set("query", deepMerge.all([options.restQuery, getParams, query])).toString();
}

export {
    WP_REST_API_USE_GLOBAL_METHOD,
    RouteLocationInterface,
    RouteRequestInterface,
    RouteParamsInterface,
    RouteResponseInterface,
    RequestArgs,
    commonUrlBuilder,
    Url
};
