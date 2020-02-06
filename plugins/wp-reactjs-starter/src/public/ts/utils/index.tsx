/* istanbul ignore file: this file does not contain any logic, only factory calls */

import { createRequestFactory, createLocalizationFactory } from "@wp-reactjs-multi-starter/utils";
import { rootStore } from "../store";

// Create REST API relevant stuff from factory
const { urlBuilder, request } = createRequestFactory(rootStore.optionStore);

// Create i18n relevant stuff from factory
const { _n, _nx, _x, __, _i } = createLocalizationFactory(rootStore.optionStore.pureSlug);

export { urlBuilder, request, _n, _nx, _x, __, _i };
