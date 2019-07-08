import {
    IRouteLocationInterface,
    ERouteHttpVerb,
    IRouteResponseInterface,
    IRouteRequestInterface,
    IRouteParamsInterface
} from "../util";

export const locationRestPluginGet: IRouteLocationInterface = {
    path: "plugin",
    method: ERouteHttpVerb.GET
};

export interface IRequestRoutePluginGet extends IRouteRequestInterface {}

export interface IParamsRoutePluginGet extends IRouteParamsInterface {}

export interface IResponseRoutePluginGet extends IRouteResponseInterface {
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
