/* eslint-disable import/no-extraneous-dependencies */
/**
 * Additional PostCSS plugin for clean-css.
 */

import postcss from "postcss";
import CleanCss from "clean-css";

// Inspired by postcss-clean, use CJS export way because the other one does not work as expected
module.exports = postcss.plugin("clean-css", (options = {}) => {
    const cleaner = new CleanCss(options);
    return (css, res) =>
        new Promise((resolve, reject) =>
            cleaner.minify(css.toString(), (error, result) => {
                if (error) {
                    return reject(new Error(error));
                }

                for (const w of result.warnings) {
                    res.warn(w);
                }

                res.root = postcss.parse(result.styles);
                resolve();
            })
        );
});
