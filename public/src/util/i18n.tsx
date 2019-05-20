// @see https://github.com/Automattic/wp-calypso/blob/master/packages/i18n-calypso/src/index.js

/**
 * Internal dependencies
 */
// @ts-ignore i18n-calpyso has no types, yet
import { I18N } from "i18n-calypso";
import React from "react";
import { pluginOptions, process } from "./";
import $ from "jquery";

const i18n = new I18N();

$.each(pluginOptions.i18n, (key: string, value: string) => {
    i18n.addTranslations({
        [key]: [value]
    });
});

// All available exports but do not export because there are no typings available officially
// export const moment = i18n.moment;
// export const numberFormat = i18n.numberFormat.bind(i18n);
// export const translate = i18n.translate.bind(i18n);
// export const configure = i18n.configure.bind(i18n);
// export const setLocale = i18n.setLocale.bind(i18n);
// export const getLocale = i18n.getLocale.bind(i18n);
// export const getLocaleSlug = i18n.getLocaleSlug.bind(i18n);
// export const addTranslations = i18n.addTranslations.bind(i18n);
// export const reRenderTranslations = i18n.reRenderTranslations.bind(i18n);
// export const registerComponentUpdateHook = i18n.registerComponentUpdateHook.bind(i18n);
// export const registerTranslateHook = i18n.registerTranslateHook.bind(i18n);
// export const state = i18n.state;
// export const stateObserver = i18n.stateObserver;
// export const localize = localizeFactory(i18n);

// For devleopment purposes show an error message if a key is accessed which is not available
if (process.env.NODE_ENV === "development") {
    i18n.state.jed.options.missing_key_callback = function(key: string) {
        console.error(
            "[Localization " +
                pluginOptions.textDomain +
                '] The following i18n key could not be found: "' +
                key +
                '". Please define it in your JsI18n.class.php!'
        );
    };
}

// Not yet released officially
// export const on = i18n.on.bind( i18n );
// export const off = i18n.off.bind( i18n );
// export const emit = i18n.emit.bind( i18n );
// export const useTranslate = useTranslateFactory( i18n );

interface ITranslateOptions {
    plural?: string;
    args?: any | any[] | string;
    components?: {
        [key: string]: React.ReactNode;
    };
    comment?: string;
    context?: string;
    count?: number;
}

/**
 * Translate an key from the JsI18n class.
 *
 * @param {string} original - the string key translation, will be used as single version if plural passed
 * @param {object} [options] - properties describing translation requirements for given text
 * @returns string
 */
function translate(original: string, options: ITranslateOptions = {}): string {
    return i18n.translate(original, options.plural, options);
}

export { i18n as default, I18N, translate };
