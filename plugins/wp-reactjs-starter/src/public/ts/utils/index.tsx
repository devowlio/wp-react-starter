/* istanbul ignore file: this file does not contain any logic, only factory calls */

import { createRequestFactory, createLocalizationFactory } from "@wp-reactjs-multi-starter/utils";
import { RootStore } from "../store";

class UtilsFactory {
    private static me: UtilsFactory;

    private requestMemo: ReturnType<typeof createRequestFactory>;

    private localizationMemo: ReturnType<typeof createLocalizationFactory>;

    // Create REST API relevant stuff from factory
    public get request() {
        return this.requestMemo
            ? this.requestMemo
            : (this.requestMemo = createRequestFactory(RootStore.get.optionStore));
    }

    // Create i18n relevant stuff from factory
    public get localization() {
        return this.localizationMemo
            ? this.localizationMemo
            : (this.localizationMemo = createLocalizationFactory(RootStore.get.optionStore.pureSlug));
    }

    public static get get() {
        return UtilsFactory.me ? UtilsFactory.me : (UtilsFactory.me = new UtilsFactory());
    }
}

const urlBuilder: UtilsFactory["requestMemo"]["urlBuilder"] = (...args) => UtilsFactory.get.request.urlBuilder(...args);
const request: UtilsFactory["requestMemo"]["request"] = (...args) => UtilsFactory.get.request.request(...args);
const _n: UtilsFactory["localizationMemo"]["_n"] = (...args) => UtilsFactory.get.localization._n(...args);
const _nx: UtilsFactory["localizationMemo"]["_nx"] = (...args) => UtilsFactory.get.localization._nx(...args);
const _x: UtilsFactory["localizationMemo"]["_x"] = (...args) => UtilsFactory.get.localization._x(...args);
const __: UtilsFactory["localizationMemo"]["__"] = (...args) => UtilsFactory.get.localization.__(...args);
const _i: UtilsFactory["localizationMemo"]["_i"] = (...args) => UtilsFactory.get.localization._i(...args);

export { urlBuilder, request, _n, _nx, _x, __, _i };
