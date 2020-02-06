import { BaseOptions } from "../../options";
import { WithOptional } from "../..";
import {
    commonUrlBuilder,
    RouteRequestInterface,
    RouteParamsInterface,
    RouteResponseInterface,
    commonRequest
} from "./";

/**
 * Create a uri builder and request function for your specific plugin depending
 * on the rest root and additional parameters.
 *
 * @param options
 * @see urlBuilder
 * @see request
 */
function createRequestFactory(options: BaseOptions) {
    const urlBuilder = (passOptions: WithOptional<Parameters<typeof commonUrlBuilder>[0], "options">) =>
        commonUrlBuilder({
            ...passOptions,
            options: {
                restNamespace: options.restNamespace,
                restNonce: options.restNonce,
                restQuery: options.restQuery,
                restRoot: options.restRoot
            }
        });

    const request = <
        Request extends RouteRequestInterface,
        Params extends RouteParamsInterface,
        Response extends RouteResponseInterface
    >(
        passOptions: WithOptional<Parameters<typeof commonRequest>[0], "options"> & {
            params?: Params;
            request?: Request;
        }
    ): Promise<Response> =>
        commonRequest({
            ...passOptions,
            options: {
                restNamespace: options.restNamespace,
                restNonce: options.restNonce,
                restQuery: options.restQuery,
                restRoot: options.restRoot
            }
        });

    return {
        urlBuilder,
        request
    };
}

export { createRequestFactory };
