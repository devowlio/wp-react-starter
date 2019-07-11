<?php
namespace MatthiasWeb\WPRJSS\general;
use MatthiasWeb\WPRJSS\base;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * i18n management for backend and frontend.
 */
class Localization extends base\Base {
    /**
     * Put your language overrides here!
     */
    private function override($locale) {
        switch ($locale) {
            // Put your overrides here!
            // case 'de_AT':
            // case 'de_CH':
            // case 'de_CH_informal':
            // case 'de_DE_formal':
            //     return 'de_DE';
            //     break;
            default:
                break;
        }
        return $locale;
    }

    /**
     * Allow language overrides so for example de_AT uses de_DE to avoid duplicate
     * .po files and management. This is for JavaScript files!
     */
    public function load_script_translation_file($file, $handle, $domain) {
        $locale = determine_locale();
        $pluginFolder = path_join(WPRJSS_PATH, base\Assets::PUBLIC_JSON_I18N);
        if ($domain === WPRJSS_TD && !is_readable($file) && substr($file, 0, strlen($pluginFolder)) === $pluginFolder) {
            // Collect data
            $folder = dirname($file);
            $use = $this->override($locale);
            $wp_scripts = wp_scripts();
            $src = $wp_scripts->registered[$handle]->src;

            // Generate new file
            $file_base = $domain . '-' . $locale . '-' . md5(basename($src)) . '.json';
            return path_join($folder, $file_base);
        }

        return $file;
    }

    /**
     * Allow language overrides so for example de_AT uses de_DE to avoid duplicate
     * .po files and management. This is for backend PHP files!
     *
     * @see https://webschale.de/2015/plugin-language-fallback-wenns-einem-die-sprache-verschlaegt/
     */
    public function override_load_textdomain($override, $domain, $mofile) {
        $pluginFolder = path_join(WPRJSS_PATH, 'languages');
        if (
            $domain === WPRJSS_TD &&
            !is_readable($mofile) &&
            substr($mofile, 0, strlen($pluginFolder)) === $pluginFolder
        ) {
            $prefix = $domain . '-';
            $folder = dirname($mofile);
            $locale = pathinfo(str_replace($prefix, '', basename($mofile)), PATHINFO_FILENAME);
            $use = $this->override($locale);

            if (!empty($use)) {
                $usemo = path_join($folder, $prefix . $use . '.mo');
                if (is_readable($usemo)) {
                    load_textdomain($domain, $usemo);
                    return true;
                }
            }
        }
        return $override;
    }
}
