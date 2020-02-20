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
    private static me: RootStore;

    public todoStore: TodoStore;

    public optionStore: OptionStore;

    private contextMemo: {
        StoreContext: React.Context<RootStore>;
        StoreProvider: React.FC<{}>;
        useStores: () => RootStore;
    };

    public get context() {
        return this.contextMemo
            ? this.contextMemo
            : (this.contextMemo = createContextFactory((this as unknown) as RootStore));
    }

    private constructor() {
        this.todoStore = new TodoStore(this);
        this.optionStore = new OptionStore(this);
    }

    public static get StoreProvider() {
        return RootStore.get.context.StoreProvider;
    }

    public static get get() {
        return RootStore.me ? RootStore.me : (RootStore.me = new RootStore());
    }
}

const useStores = () => RootStore.get.context.useStores();

export { RootStore, useStores };
