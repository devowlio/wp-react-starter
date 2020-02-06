import { commonRequest } from "../../../../lib/factory/ajax/commonRequest";
import { RouteHttpVerb } from "../../../../lib";
import { produce } from "immer";

jest.mock("../../../../lib/factory/ajax/commonUrlBuilder");
jest.mock("jquery");

const { commonUrlBuilder } = require("../../../../lib/factory/ajax/commonUrlBuilder");
const $ = require("jquery");

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

    it("should fetch correct response with default arguments", async () => {
        const url = baseOpts.options.restRoot + baseOpts.options.restNamespace + baseOpts.location.path;

        commonUrlBuilder.mockImplementation(() => url);

        await commonRequest(baseOpts);
        expect(commonUrlBuilder).toHaveBeenCalledWith(expect.objectContaining(baseOpts));
        expect(commonUrlBuilder).toHaveBeenCalledWith(expect.objectContaining({ nonce: false }));
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            { data: undefined, headers: { "X-WP-Nonce": baseOpts.options.restNonce }, url }
        );
        expect($.ajax).toHaveBeenCalled();
    });

    it("should fetch correct response with request body", async () => {
        const request = { id: 1 };
        const opts = produce(baseOpts, (draft) => {
            (draft as any).request = request;
        });

        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);

        await commonRequest(opts);
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            { data: request, headers: { "X-WP-Nonce": opts.options.restNonce }, url }
        );
    });

    it("should fetch correct response with GET parameters", async () => {
        const params = { id: 5 };
        const opts = produce(baseOpts, (draft) => {
            draft.location.path = "/user/:id";
            (draft as any).params = params;
        });
        const url = `${opts.options.restRoot + opts.options.restNamespace}/user/${params.id}`;

        commonUrlBuilder.mockImplementation(() => url);

        await commonRequest(opts);
        expect($.extend).toHaveBeenCalledWith(
            true,
            {},
            { data: undefined, headers: { "X-WP-Nonce": opts.options.restNonce }, url }
        );
    });

    it("should fetch correct response with custom settings", async () => {
        const settings = {
            headers: {
                Connection: "keep-alive"
            }
        };
        const opts = produce(baseOpts, (draft) => {
            (draft as any).settings = settings;
        });
        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);

        await commonRequest(opts);
        expect($.extend).toHaveBeenCalledWith(true, settings, {
            data: undefined,
            headers: { "X-WP-Nonce": opts.options.restNonce },
            url
        });
    });

    it("should fetch correct response with POST method", async () => {
        const method = RouteHttpVerb.POST;
        const opts = produce(baseOpts, (draft) => {
            (draft.location as any).method = method;
        });
        const url = opts.options.restRoot + opts.options.restNamespace + opts.location.path;

        commonUrlBuilder.mockImplementation(() => url);

        await commonRequest(opts);
        expect($.extend).toHaveBeenCalledWith(
            true,
            { method: method },
            { data: undefined, headers: { "X-WP-Nonce": opts.options.restNonce }, url }
        );
    });
});
