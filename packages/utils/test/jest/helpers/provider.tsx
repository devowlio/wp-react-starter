import React from "react";

/**
 * Use this common Provider in all your snapshot tests with all
 * your available providers. Currently there is no provider for the utils package.
 *
 * @param children
 */
const Provider: React.FC = ({ children }) => <>{children}</>;

export { Provider };
