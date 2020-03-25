import { RootStore, useStores } from "../../../src/public/ts/store/stores";

jest.mock(`${process.env.PACKAGE_SCOPE}/utils`, () => ({
    createContextFactory: jest.fn().mockImplementation(() => ({}))
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
const { createContextFactory } = require("@wp-reactjs-multi-starter/utils");

describe("stores", () => {
    beforeEach(() => {
        // @ts-ignore Currently the easiest way to reset private singleton variable
        RootStore.me = undefined;
    });

    describe("configure", () => {
        it("should call configure to always force actions", () => {
            expect(mobx.configure).toHaveBeenCalledWith(expect.objectContaining({ enforceActions: "always" }));
        });
    });

    describe("RootStore", () => {
        describe("context", () => {
            it("should cache context factory result", () => {
                RootStore.get.context;
                RootStore.get.context;

                expect(createContextFactory).toHaveBeenCalledTimes(1);
            });
        });

        describe("constructor", () => {
            it("should act as singleton", () => {
                RootStore.get;
                RootStore.get;

                expect(TodoStore).toHaveBeenCalledTimes(1);
            });

            it("should initiate multiple stores", () => {
                RootStore.get;

                expect(TodoStore).toHaveBeenCalledWith(expect.any(Object));
                expect(OptionStore).toHaveBeenCalledWith(expect.any(Object));
            });
        });

        describe("StoreProvider", () => {
            it("should obtain StoreProvider from factory", () => {
                jest.spyOn(RootStore.prototype, "context", "get").mockReturnValue({
                    StoreContext: null,
                    StoreProvider: jest.fn(),
                    useStores: null
                });

                const actual = RootStore.StoreProvider;

                expect(actual).toBeDefined();
            });
        });
    });

    describe("useStores", () => {
        it("should get StoreContext from factory result", () => {
            const mockUseStores = jest.fn();
            jest.spyOn(RootStore.prototype, "context", "get").mockReturnValue({
                StoreContext: null,
                StoreProvider: null,
                useStores: mockUseStores
            });

            useStores();

            expect(mockUseStores).toHaveBeenCalled();
        });
    });
});
