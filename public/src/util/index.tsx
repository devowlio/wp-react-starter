const pluginOptions = (window as any).wprjssOpts,
    process = (window as any).process;

const untrailingslashit = (str: string): string =>
    str.endsWith("/") || str.endsWith("\\") ? untrailingslashit(str.slice(0, -1)) : str;
const trailingslashit = (str: string): string => untrailingslashit(str) + "/";

export { pluginOptions, process, trailingslashit, untrailingslashit };
export * from "./ajax";
