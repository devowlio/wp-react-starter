<?php
namespace MatthiasWeb\WPRJSS;

use MatthiasWeb\Utils\Localization as UtilsLocalization;
use MatthiasWeb\WPRJSS\base\UtilsProvider;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * i18n management for backend and frontend.
 */
class Localization {
    use UtilsProvider;
    use UtilsLocalization;

    /**
     * Put your language overrides here!
     *
     * @param string $locale
     * @return string
     */
    protected function override($locale) {
        switch ($locale) {
            // Put your overrides here!
            // case 'de_AT':
            // case 'de_CH':
            // case 'de_CH_informal':
            // case 'de_DE_formal':
            //     return 'de_DE';
            //     break;
            // default:
            //     break;
        }
        return $locale;
    }

    /**
     * Get the directory where the languages folder exists.
     *
     * @param string $type
     * @return string[]
     */
    protected function getPackageInfo($type) {
        if ($type === UtilsLocalization::$PACKAGE_INFO_BACKEND) {
            return [path_join(WPRJSS_PATH, 'languages'), WPRJSS_TD];
        } else {
            return [path_join(WPRJSS_PATH, Assets::$PUBLIC_JSON_I18N), WPRJSS_TD];
        }
    }
}
