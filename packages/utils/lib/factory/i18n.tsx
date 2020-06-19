// @see https://github.com/Automattic/wp-calypso/blob/master/packages/i18n-calypso/src/index.js

import { ReactNode, ReactElement } from "react";
import interpolate from "interpolate-components";
import * as wpi18n from "@wordpress/i18n";
import wp from "wp";

/**
 * Create multiple functions for a specific plugin so they can be localized.
 *
 * @param slug The slug which you have registered your i18n assets
 * @returns
 */
function createLocalizationFactory(slug: string) {
    const { wpi18nLazy } = window as any;
    if (wpi18nLazy && wpi18nLazy[slug] && wp && wp.i18n) {
        for (const localeData of wpi18nLazy[slug]) {
            wp.i18n.setLocaleData(localeData, slug);
        }
    }

    /**
     * Translates and retrieves the singular or plural form based on the supplied number.
     * For arguments sprintf is used, see http://www.diveintojavascript.com/projects/javascript-sprintf for
     * specification and usage.
     *
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#_n
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#sprintf
     */
    function _n(single: string, plural: string, count: number, ...args: any[]): string {
        return wpi18n.sprintf(wpi18n._n(single, plural, count, slug), ...args) as string;
    }

    /**
     * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
     * For arguments sprintf is used, see http://www.diveintojavascript.com/projects/javascript-sprintf for
     * specification and usage.
     *
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#_n
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#sprintf
     */
    function _nx(single: string, plural: string, count: number, context: string, ...args: any[]): string {
        return wpi18n.sprintf(wpi18n._nx(single, plural, count, context, slug), ...args) as string;
    }

    /**
     * Retrieve translated string with gettext context.
     * For arguments sprintf is used, see http://www.diveintojavascript.com/projects/javascript-sprintf for
     * specification and usage.
     *
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#_n
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#sprintf
     */
    function _x(single: string, context: string, ...args: any[]): string {
        return wpi18n.sprintf(wpi18n._x(single, context, slug), ...args) as string;
    }

    /**
     * Retrieve the translation of text.
     * For arguments sprintf is used, see http://www.diveintojavascript.com/projects/javascript-sprintf for
     * specification and usage.
     *
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#_n
     * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#sprintf
     */
    function __(single: string, ...args: any[]): string {
        return wpi18n.sprintf(wpi18n.__(single, slug), ...args) as string;
    }

    /**
     * This function allows you to interpolate react components to your translations.
     * You have to pass an already translated string as argument! For this you can use the other
     * i18n functions like _n() or __().
     *
     * A translation can look like this: "Hello {{a}}click me{{/a}}." and you have to pass
     * a component with key "a".
     */
    function _i(translation: string, components?: { [placeholder: string]: ReactElement }): any {
        return interpolate({
            mixedString: translation,
            components
        }) as ReactNode;
    }

    return { _n, _nx, _x, __, _i };
}

export { createLocalizationFactory };
