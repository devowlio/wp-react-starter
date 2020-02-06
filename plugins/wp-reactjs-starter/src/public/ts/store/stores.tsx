import { configure } from "mobx";
import { createContextFactory } from "@wp-reactjs-multi-starter/utils";
import { TodoStore } from "./todo";
import { OptionStore } from "./option";

configure({
    enforceActions: "always"
});

/**
 * A collection of all available stores which gets available
 * through the custom hook useStores in your function components.
 *
 * @see https://mobx.js.org/best/store.html#combining-multiple-stores
 */
class RootStore {
    public todoStore: TodoStore;
    public optionStore: OptionStore;

    constructor() {
        this.todoStore = new TodoStore(this);
        this.optionStore = new OptionStore(this);
    }
}

const rootStore = new RootStore();

const { StoreContext, StoreProvider, useStores } = createContextFactory(rootStore);

export { RootStore, rootStore, StoreContext, StoreProvider, useStores };
