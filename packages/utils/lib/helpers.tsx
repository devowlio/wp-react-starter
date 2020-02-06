const untrailingslashit = (str: string): string =>
    str.endsWith("/") || str.endsWith("\\") ? untrailingslashit(str.slice(0, -1)) : str;
const trailingslashit = (str: string): string => `${untrailingslashit(str)}/`;

// Allows to make an interface extension and make some properties optional (https://git.io/JeK6J)
type AllKeyOf<T> = T extends never ? never : keyof T;
type Optional<T, K> = { [P in Extract<keyof T, K>]?: T[P] };
type WithOptional<T, K extends AllKeyOf<T>> = T extends never ? never : Omit<T, K> & Optional<T, K>;

export { untrailingslashit, trailingslashit, AllKeyOf, Optional, WithOptional };
