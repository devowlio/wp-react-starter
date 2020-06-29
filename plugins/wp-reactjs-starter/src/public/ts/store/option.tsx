import { observable, runInAction } from "mobx";
import { BaseOptions } from "@wp-reactjs-multi-starter/utils";
import { RootStore } from "./stores";

class OptionStore extends BaseOptions {
    // Implement "others" property in your Assets.php;
    @observable
    public others: {} = {};

    public readonly pureSlug: ReturnType<typeof BaseOptions.getPureSlug>;

    public readonly pureSlugCamelCased: ReturnType<typeof BaseOptions.getPureSlug>;

    public readonly rootStore: RootStore;

    constructor(rootStore: RootStore) {
        super();
        this.rootStore = rootStore;
        this.pureSlug = BaseOptions.getPureSlug(process.env);
        this.pureSlugCamelCased = BaseOptions.getPureSlug(process.env, true);

        // Use the localized WP object to fill this object values.
        runInAction(() => Object.assign(this, (window as any)[this.pureSlugCamelCased]));
    }
}

export { OptionStore };
