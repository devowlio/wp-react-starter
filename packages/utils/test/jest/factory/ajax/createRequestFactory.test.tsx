import { createRequestFactory } from "../../../../lib/factory/ajax/createRequestFactory";

jest.mock("../../../../lib/factory/ajax/commonUrlBuilder");
jest.mock("../../../../lib/factory/ajax/commonRequest");

const { commonUrlBuilder } = require("../../../../lib/factory/ajax/commonUrlBuilder");
const { commonRequest } = require("../../../../lib/factory/ajax/commonRequest");

describe("createRequestFactory", () => {
    const restOptions = {
        restRoot: "http://localhost/wp-json/",
        restQuery: {},
        restNamespace: "jest/v1",
        restNonce: "jd§4,dwD"
    };
    const opts = {
        slug: "jest",
        textDomain: "jest",
        version: "1.0.0",
        ...restOptions
    };
    const urlBuilderOpts = {
        location: {
            path: "/users"
        }
    };
    const url = opts.restRoot + opts.restNamespace + urlBuilderOpts.location.path;
    const response = [
        {
            id: "5",
            name: "John Smith"
        }
    ];

    it("should create correct factory urlBuilder", () => {
        commonUrlBuilder.mockImplementation(() => url);

        const { urlBuilder } = createRequestFactory(opts);
        const actual = urlBuilder(urlBuilderOpts);

        expect(commonUrlBuilder).toHaveBeenCalledWith({ ...urlBuilderOpts, options: restOptions });
        expect(actual).toEqual(url);
    });

    it("should create correct factory request", () => {
        commonRequest.mockImplementation(() => response);

        const { request } = createRequestFactory(opts);
        const actual = request(urlBuilderOpts);

        expect(commonRequest).toHaveBeenCalledWith({ ...urlBuilderOpts, options: restOptions });
        expect(actual).toEqual(response);
    });
});
