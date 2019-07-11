// @see https://github.com/Automattic/wp-calypso/blob/master/packages/i18n-calypso/src/index.js

/**
 * Internal dependencies
 */
// @ts-ignore i18n-calpyso has no types, yet
import { I18N } from "i18n-calypso";
import React from "react";
import { pluginOptions, process } from "./";
// @ts-ignore @wordpress/i18n has no types, yet
import * as wpi18n from "@wordpress/i18n";

/**
 * Translates and retrieves the singular or plural form based on the supplied number.
 * For arguments sprintf is used, see http://www.diveintojavascript.com/projects/javascript-sprintf for
 * specification and usage.
 *
 * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#_n
 * @see https://github.com/WordPress/gutenberg/tree/master/packages/i18n#sprintf
 */
function _n(single: string, plural: string, count: number, ...args: any[]): string {
    return wpi18n.sprintf(wpi18n._n(single, plural, count, pluginOptions.slug), ...args) as string;
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
    return wpi18n.sprintf(wpi18n._nx(single, plural, count, context, pluginOptions.slug), ...args) as string;
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
    return wpi18n.sprintf(wpi18n.__(single, pluginOptions.slug), ...args) as string;
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
    return wpi18n.sprintf(wpi18n._x(single, context, pluginOptions.slug), ...args) as string;
}

const i18n = new I18N();

/**
 * This function allows you to interpolate react components to your translations.
 * You have to pass an already translated string as argument! For this you can use the other
 * i18n functions like _n() or __().
 *
 * A translation can look like this: "Hello {{a}}click me{{/a}}." and you have to pass
 * a component with key "a".
 */
function _i(
    translation: string,
    components?: {
        [key: string]: React.ReactNode;
    }
): any {
    return i18n.translate(translation, {
        components
    }) as React.ReactNode;
}

export { _n, _nx, __, _x, _i };
