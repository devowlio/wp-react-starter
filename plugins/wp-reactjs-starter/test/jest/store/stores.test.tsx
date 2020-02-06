import "../../../src/public/ts/store/stores";

jest.mock(`${process.env.PACKAGE_SCOPE}/utils`, () => ({
    createContextFactory: () =>
        ({
            StoreContext: undefined,
            StoreProvider: undefined,
            useStores: undefined
        } as any)
}));
jest.mock("../../../src/public/ts/store/option");
jest.mock("../../../src/public/ts/store/todo", () => ({
    TodoStore: jest.fn()
}));
jest.mock("../../../src/public/ts/store/option", () => ({
    OptionStore: jest.fn()
}));
jest.mock("mobx", () => ({
    configure: jest.fn()
}));

const mobx = require("mobx");
const { TodoStore } = require("../../../src/public/ts/store/todo");
const { OptionStore } = require("../../../src/public/ts/store/option");

describe("stores", () => {
    describe("RootStore", () => {
        describe("constructor", () => {
            it("should automatically create a root store instance", () => {
                expect(TodoStore).toHaveBeenCalledWith(expect.any(Object));
                expect(OptionStore).toHaveBeenCalledWith(expect.any(Object));
            });
        });
    });

    describe("configure", () => {
        it("should call configure to always force actions", () => {
            expect(mobx.configure).toHaveBeenCalledWith(expect.objectContaining({ enforceActions: "always" }));
        });
    });
});
