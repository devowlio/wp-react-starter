import { createLocalizationFactory } from "../../../lib/factory/i18n";

jest.mock("i18n-calypso");
jest.mock("@wordpress/i18n");

const i18nCalypso = require("i18n-calypso");
const wpi18n = require("@wordpress/i18n");

describe("i18n", () => {
    const slug = "jest";
    const single = "Hello %s World";
    const plural = "Hello %s Worlds";
    const context = "title";
    const count = 0;
    const arg1 = "Bob's";

    it("should create correct factory _n", () => {
        wpi18n._n.mockImplementation(() => single);
        wpi18n.sprintf.mockImplementation(() => single);

        const { _n } = createLocalizationFactory(slug);
        const actual = _n(single, plural, count, arg1);

        expect(wpi18n._n).toHaveBeenCalledWith(single, plural, count, slug);
        expect(wpi18n.sprintf).toHaveBeenCalledWith(single, arg1);
        expect(actual).toEqual(single);
    });

    it("should create correct factory _nx", () => {
        wpi18n._nx.mockImplementation(() => single);
        wpi18n.sprintf.mockImplementation(() => single);

        const { _nx } = createLocalizationFactory(slug);
        const actual = _nx(single, plural, count, context, arg1);

        expect(wpi18n._nx).toHaveBeenCalledWith(single, plural, count, context, slug);
        expect(wpi18n.sprintf).toHaveBeenCalledWith(single, arg1);
        expect(actual).toEqual(single);
    });

    it("should create correct factory _x", () => {
        wpi18n._x.mockImplementation(() => single);
        wpi18n.sprintf.mockImplementation(() => single);

        const { _x } = createLocalizationFactory(slug);
        const actual = _x(single, context, arg1);

        expect(wpi18n._x).toHaveBeenCalledWith(single, context, slug);
        expect(wpi18n.sprintf).toHaveBeenCalledWith(single, arg1);
        expect(actual).toEqual(single);
    });

    it("should create correct factory __", () => {
        wpi18n.__.mockImplementation(() => single);
        wpi18n.sprintf.mockImplementation(() => single);

        const { __ } = createLocalizationFactory(slug);
        const actual = __(single, arg1);

        expect(wpi18n.__).toHaveBeenCalledWith(single, slug);
        expect(wpi18n.sprintf).toHaveBeenCalledWith(single, arg1);
        expect(actual).toEqual(single);
    });

    it("should create correct factory _i", () => {
        const components = {};
        const reactNode = {};

        i18nCalypso.translate.mockImplementation(() => reactNode);

        const { _i } = createLocalizationFactory(slug);
        const actual = _i(single, components);

        expect(i18nCalypso.translate).toHaveBeenCalledWith(single, { components });
        expect(actual).toBe(reactNode);
    });
});
