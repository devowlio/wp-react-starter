import uri from "lil-uri";

const pluginOptions = (window as any).wprjssOpts,
    process = (window as any).process,
    // Use _method instead of Http verb
    WP_REST_API_USE_GLOBAL_METHOD = true;

const untrailingslashit = (str: string): string =>
    str.endsWith("/") || str.endsWith("\\") ? untrailingslashit(str.slice(0, -1)) : str;
const trailingslashit = (str: string): string => untrailingslashit(str) + "/";

function ajax<B = false extends false ? false : true, C = B extends true ? string : JQuery.jqXHR<any>>(
    url: string,
    settings: JQuery.AjaxSettings<any> = {},
    urlNamespace = "wprjss/v1",
    returnUrl?: B
): C {
    const apiUrl = uri(pluginOptions.restRoot),
        windowProtocol = uri(window.location.href).protocol(),
        query = apiUrl.query() || {},
        path = (query.rest_route as string) || apiUrl.path(), // Determine path from permalink settings
        usePath = trailingslashit(path) + trailingslashit(urlNamespace) + url;

    // Set https if site url is SSL
    if (windowProtocol === "https") {
        apiUrl.protocol("https");
    }

    // Set path depending on permalink settings
    if (query.rest_route) {
        query.rest_route = usePath;
    } else {
        apiUrl.path(usePath); // Set path
    }

    // Use global parameter (see https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/)
    if (WP_REST_API_USE_GLOBAL_METHOD && settings.method && settings.method.toUpperCase() !== "GET") {
        query._method = settings.method;
        settings.method = "POST";
    }

    const builtUrl = apiUrl.query($.extend(true, {}, pluginOptions.restQuery, query)).build();
    if (returnUrl) {
        return (builtUrl as unknown) as C;
    }

    return ($.ajax(
        $.extend(true, settings, {
            url: builtUrl,
            headers: {
                "X-WP-Nonce": pluginOptions.restNonce
            }
        })
    ) as unknown) as C;
}

export { pluginOptions, process, ajax, trailingslashit, untrailingslashit };
