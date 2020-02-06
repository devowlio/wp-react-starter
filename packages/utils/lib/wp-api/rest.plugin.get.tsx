import {
    RouteLocationInterface,
    RouteHttpVerb,
    RouteResponseInterface,
    RouteRequestInterface,
    RouteParamsInterface
} from "../factory";

export const locationRestPluginGet: RouteLocationInterface = {
    path: "/plugin",
    method: RouteHttpVerb.GET
};

export type RequestRoutePluginGet = RouteRequestInterface;

export type ParamsRoutePluginGet = RouteParamsInterface;

export interface ResponseRoutePluginGet extends RouteResponseInterface {
    Name: string;
    PluginURI: string;
    Version: string;
    Description: string;
    Author: string;
    AuthorURI: string;
    TextDomain: string;
    DomainPath: string;
    Network: boolean;
    Title: string;
    AuthorName: string;
}
