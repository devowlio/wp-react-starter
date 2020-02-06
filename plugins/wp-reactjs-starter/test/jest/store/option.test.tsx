import { OptionStore } from "../../../src/public/ts/store/option";
import { RootStore } from "../../../src/public/ts/store";

jest.mock(`${process.env.PACKAGE_SCOPE}/utils`);
jest.mock("../../../src/public/ts/store/stores");
jest.mock("mobx", () => ({
    observable: jest.fn(),
    runInAction: jest.fn().mockImplementation((callback) => callback())
}));

const mobx = require("mobx");
const { BaseOptions } = require(`${process.env.PACKAGE_SCOPE}/utils`);

describe("OptionStore", () => {
    it("should call the constructor correctly", () => {
        const slug = "jest";

        BaseOptions.getPureSlug.mockImplementation(() => slug);

        const actual = new OptionStore({} as RootStore);

        expect(actual.pureSlug).toEqual(slug);
        expect(actual.pureSlugCamelCased).toEqual(slug);
        expect(actual.rootStore).toEqual({});
        expect(mobx.runInAction).toHaveBeenCalledWith(expect.any(Function));
    });
});
