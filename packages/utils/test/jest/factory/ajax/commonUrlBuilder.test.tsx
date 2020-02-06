import { commonUrlBuilder, RouteHttpVerb } from "../../../../lib/factory/ajax/commonUrlBuilder";
import { produce, setAutoFreeze } from "immer";

jest.mock("url-parse");
jest.mock("../../../../lib/helpers");
jest.mock("jquery");

const Url = require("url-parse");
const helpers = require("../../../../lib/helpers");
const $ = require("jquery");

describe("commonUrlBuilder", () => {
    const baseOpts = {
        location: {
            path: "/user"
        },
        options: {
            restRoot: "http://localhost/wp-json/",
            restNamespace: "jest/v1",
            restNonce: "jd§4,dwD",
            restQuery: undefined as any
        }
    };

    function createUrlMock(produceData = (draft: any) => draft) {
        const urlSetMock = jest.fn();
        Url.mockImplementation(() => {
            setAutoFreeze(false);
            const data: any = produce(
                {
                    query: {} as any,
                    pathname: "/wp-json/",
                    protocol: "http:",
                    set: urlSetMock.mockImplementation((part: string, value: string) => {
                        data[part] = value;

                        if (part == "query") {
                            return {
                                toString: jest.fn()
                            };
                        }
                        return undefined;
                    })
                },
                produceData
            );
            setAutoFreeze(true);

            return data;
        });

        return { urlSetMock };
    }

    it("should create a valid url with default arguments", () => {
        const { urlSetMock } = createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(baseOpts);

        expect(urlSetMock).toHaveBeenCalledWith("pathname", "/wp-json/jest/v1/user");
        expect(urlSetMock).toHaveBeenCalledWith("query", {});
        expect(helpers.trailingslashit).toHaveBeenCalled();
        expect(helpers.untrailingslashit).toHaveBeenCalled();
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            undefined,
            {},
            { _wpnonce: baseOpts.options.restNonce, _method: undefined }
        );
    });

    it("should create a valid url with HTTPS auto correction", () => {
        const { urlSetMock } = createUrlMock((draft) => {
            draft.protocol = "https:";
        });
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(baseOpts);

        expect(urlSetMock).toHaveBeenCalledWith("protocol", "https");
    });

    it("should create a valid url with an additional predefined query", () => {
        const opts = produce(baseOpts, (draft) => {
            draft.options.restQuery = {
                trackId: 5
            };
        });

        const { urlSetMock } = createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect(urlSetMock).toHaveBeenCalledWith("pathname", "/wp-json/jest/v1/user");
        expect(urlSetMock).toHaveBeenCalledWith("query", {});
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            opts.options.restQuery,
            {},
            { _wpnonce: opts.options.restNonce }
        );
    });

    it("should create a valid url with index.php permalink settings (rest_route query)", () => {
        const opts = produce(baseOpts, (draft) => {
            draft.options.restRoot = "http://localhost/?rest_root=/wp-json/";
        });

        const { urlSetMock } = createUrlMock((draft) => {
            draft.query = {
                // eslint-disable-next-line @typescript-eslint/camelcase
                rest_route: "/wp-json/"
            };
        });

        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect(urlSetMock).toHaveBeenCalledWith("query", {});
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            undefined,
            {},
            {
                _wpnonce: opts.options.restNonce,
                // eslint-disable-next-line @typescript-eslint/camelcase
                rest_route: "/wp-json/jest/v1/user"
            }
        );
    });

    it("should create a valid url without nonce", () => {
        const opts = produce(baseOpts, (draft) => {
            (draft as any).nonce = false;
        });

        createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            undefined,
            {},
            expect.not.objectContaining({ _wpnonce: opts.options.restNonce })
        );
    });

    it("should create a valid url with other method than GET (POST)", () => {
        const method = RouteHttpVerb.POST;
        const opts = produce(baseOpts, (draft) => {
            (draft.location as any).method = method;
        });

        createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            undefined,
            {},
            { _wpnonce: opts.options.restNonce, _method: method }
        );
    });

    it("should create a valid url with an url parameter (:user)", () => {
        const params = {
            id: 50
        };
        const opts = produce(baseOpts, (draft) => {
            draft.location.path = "/user/:id";
            (draft as any).params = params;
        });

        const { urlSetMock } = createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect(urlSetMock).toHaveBeenCalledWith("pathname", "/wp-json/jest/v1/user/50");
        expect($.extend).toHaveBeenCalledWith(true, {}, undefined, {}, { _wpnonce: opts.options.restNonce });
    });

    it("should create a valid url with an url parameter (:user) combined with additional query", () => {
        const params = {
            id: 50,
            fields: "id"
        };
        const opts = produce(baseOpts, (draft) => {
            draft.location.path = "/user/:id";
            (draft as any).params = params;
        });

        const { urlSetMock } = createUrlMock();
        helpers.trailingslashit.mockImplementation(() => "/wp-json/");
        helpers.untrailingslashit.mockImplementation(() => "jest/v1");
        $.extend.mockImplementation((): any => ({}));

        commonUrlBuilder(opts);

        expect(urlSetMock).toHaveBeenCalledWith("pathname", "/wp-json/jest/v1/user/50");
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            undefined,
            { fields: "id" },
            { _wpnonce: opts.options.restNonce }
        );
    });
});
