import { createContext, FC, useContext } from "react";

/* istanbul ignore next: no logic in this factory! */
/**
 * Create context relevant objects to use within React.
 *
 * @param object
 * @returns
 */
function createContextFactory<T>(object: T) {
    /**
     * MobX stores collection
     */
    const StoreContext = createContext(object);

    /**
     * MobX HOC to get the context via hook.
     *
     * @param children
     */
    const StoreProvider: FC<{}> = ({ children }) => (
        <StoreContext.Provider value={object}>{children}</StoreContext.Provider>
    );

    /**
     * Get all the MobX stores via a single hook.
     */
    const useStores = () => useContext(StoreContext);

    return { StoreContext, StoreProvider, useStores };
}

export { createContextFactory };
