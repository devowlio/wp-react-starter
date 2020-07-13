import { commonRequest } from "../../../../lib/factory/ajax/commonRequest";
import { RouteHttpVerb } from "../../../../lib/factory/ajax/routeHttpVerbEnum";
import { produce, setAutoFreeze } from "immer";

jest.mock("../../../../lib/factory/ajax/commonUrlBuilder");
jest.mock("../../../../lib/factory/ajax/parseResult");
jest.mock("deepmerge");
jest.mock("url-parse");
jest.mock("whatwg-fetch");

const { commonUrlBuilder } = require("../../../../lib/factory/ajax/commonUrlBuilder");
const { parseResult } = require("../../../../lib/factory/ajax/parseResult");
const deepMerge = require("deepmerge");
const Url = require("url-parse");

parseResult.mockReturnValue({});

describe("commonRequest", () => {
    const baseOpts = {
        location: {
            path: "/user"
        },
        options: {
            restRoot: "http://localhost/wp-json/",
            restNamespace: "jest/v1",
            restNonce: "jd§4,dwD"
        }
    };

    function createUrlMock(produceData = (draft: any) => draft) {
        const urlSetMock = jest.fn();
        const urlToStringMock = jest.fn();
        Url.mockImplementation(() => {
            setAutoFreeze(false);
            const data: any = produce(
                {
                    query: {} as any,
                    pathname: "/wp-json/",
                    protocol: "http:",
                    set: urlSetMock.mockImplementation((part: string, value: string) => {
                        data[part] = value;
                        return data;
                    }),
                    toString: urlToStringMock.mockImplementation(() => "https://")
                },
                produceData
            );
            setAutoFreeze(true);

            return data;
        });

        return { urlSetMock, urlToStringMock };
    }

    it("should fetch correct response with default arguments", async () => {
        const url = baseOpts.options.restRoot + baseOpts.options.restNamespace + baseOpts.location.path;

        const { urlSetMock, urlToStringMock } = createUrlMock();
        commonUrlBuilder.mockImplementation(() => url);
        deepMerge.all.mockImplementation((): any => ({}));
        const spyFetch = jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: true } as any));

        const actual = await commonRequest(baseOpts);

        expect(commonUrlBuilder).toHaveBeenCalledWith(expect.objectContaining(baseOpts));
        expect(commonUrlBuilder).toHaveBeenCalledWith(expect.objectContaining({ nonce: false }));
        expect(Url).toHaveBeenCalledWith(url, true);
        expect(deepMerge.all).toHaveBeenCalledWith([
            {
                method: "GET"
            },
            {
                body: undefined,
                headers: { "Content-Type": "application/json;charset=utf-8", "X-WP-Nonce": baseOpts.options.restNonce }
            }
        ]);
        expect(urlSetMock).not.toHaveBeenCalled();
        expect(urlToStringMock).toHaveBeenCalled();
        expect(spyFetch).toHaveBeenCalledWith("https://", {});
        expect(parseResult).toHaveBeenCalled();
        expect(actual).toEqual({});
    });

    it("should fetch correct response with request body as GET parameters", async () => {
        const request = { id: 1 };
        const opts = produce(baseOpts, (draft) => {
            (draft as any).request = request;
        });

        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);
        const { urlSetMock } = createUrlMock();
        deepMerge.mockImplementation((): any => ({}));
        deepMerge.all.mockImplementation((): any => ({}));
        const spyFetch = jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: true } as any));

        await commonRequest(opts);

        expect(deepMerge).toHaveBeenCalledWith({}, request);
        expect(urlSetMock).toHaveBeenCalledWith("query", {});
        expect(deepMerge.all).toHaveBeenCalledWith([
            { method: "GET" },
            {
                body: undefined,
                headers: { "Content-Type": "application/json;charset=utf-8", "X-WP-Nonce": opts.options.restNonce }
            }
        ]);
        expect(spyFetch).toHaveBeenCalledWith("https://", {});
    });

    it("should fetch correct response with GET parameters", async () => {
        const params = { id: 5 };
        const opts = produce(baseOpts, (draft) => {
            draft.location.path = "/user/:id";
            (draft as any).params = params;
        });
        const url = `${opts.options.restRoot + opts.options.restNamespace}/user/${params.id}`;

        commonUrlBuilder.mockImplementation(() => url);
        const { urlSetMock } = createUrlMock();
        deepMerge.mockImplementation((): any => ({}));
        deepMerge.all.mockImplementation((): any => ({}));
        const spyFetch = jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: true } as any));

        await commonRequest(opts);

        expect(deepMerge).not.toHaveBeenCalled();
        expect(urlSetMock).not.toHaveBeenCalled();
        expect(deepMerge.all).toHaveBeenCalledWith([
            { method: "GET" },
            {
                body: undefined,
                headers: { "Content-Type": "application/json;charset=utf-8", "X-WP-Nonce": opts.options.restNonce }
            }
        ]);
        expect(spyFetch).toHaveBeenCalledWith("https://", {});
    });

    it("should fetch correct response with custom settings", async () => {
        const settings = {
            headers: {
                Connection: "keep-alive"
            }
        };
        setAutoFreeze(false);
        const opts = produce(baseOpts, (draft) => {
            (draft as any).settings = settings;
        });
        setAutoFreeze(true);
        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);
        createUrlMock();
        deepMerge.mockImplementation((): any => ({}));
        deepMerge.all.mockImplementation((): any => ({}));
        const spyFetch = jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: true } as any));

        await commonRequest(opts);

        expect(deepMerge.all).toHaveBeenCalledWith([
            { method: "GET", headers: settings.headers },
            {
                body: undefined,
                headers: { "Content-Type": "application/json;charset=utf-8", "X-WP-Nonce": opts.options.restNonce }
            }
        ]);
        expect(spyFetch).toHaveBeenCalledWith("https://", {});
    });

    it("should fetch correct response with POST method", async () => {
        const method = RouteHttpVerb.POST;
        const opts = produce(baseOpts, (draft) => {
            (draft.location as any).method = method;
        });
        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);
        createUrlMock();
        deepMerge.mockImplementation((): any => ({}));
        deepMerge.all.mockImplementation((): any => ({}));
        jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: true } as any));

        await commonRequest(opts);

        expect(deepMerge.all).toHaveBeenCalledWith([
            { method },
            {
                body: undefined,
                headers: { "Content-Type": "application/json;charset=utf-8", "X-WP-Nonce": opts.options.restNonce }
            }
        ]);
    });

    it("should throw error when response is not ok", async () => {
        const method = RouteHttpVerb.GET;
        const opts = produce(baseOpts, (draft) => {
            (draft.location as any).method = method;
        });
        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);
        createUrlMock();
        deepMerge.mockImplementation((): any => ({}));
        deepMerge.all.mockImplementation((): any => ({}));
        jest.spyOn(window, "fetch").mockImplementationOnce(() => ({ ok: false } as any));

        await expect(commonRequest(opts)).rejects.toEqual({
            ok: false,
            responseJSON: {}
        });
        expect(parseResult).toHaveBeenCalled();
        expect((global as any).detectCorrupRestApiFailed).toBe(1);
    });
});
