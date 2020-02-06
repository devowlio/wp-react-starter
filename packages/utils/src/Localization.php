<?php
namespace MatthiasWeb\Utils;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base i18n management for backend and frontend.
 */
trait Localization {
    public static $PACKAGE_INFO_FRONTEND = 'frontend';
    public static $PACKAGE_INFO_BACKEND = 'backend';

    /**
     * Put your language overrides here!
     *
     * @param string $locale
     * @return string
     */
    abstract protected function override($locale);

    /**
     * Get the directory where the languages folder exists.
     *
     * The returned string array should look like this:
     * [0] = Full path to the "languages" folder
     * [1] = Used textdomain
     * [2]? = Use different prefix domain in filename
     *
     * @param string $type
     * @return string[]
     */
    abstract protected function getPackageInfo($type);

    /**
     * Common helper to get an array of variables for package info.
     *
     * @param string $type
     * @param string $resolve
     * @param string $domain
     * @return string[]
     */
    public function packageInfoParserForOverrides($type, $resolve, $domain) {
        $packageInfo = $this->getPackageInfo($type);
        $resolve = $this->resolveSymlinks($resolve);
        $packagePath = $this->resolveSymlinks($packageInfo[0]);
        $packageDomain = $packageInfo[1];
        $packageDomainFilePrefix = isset($packageInfo[2]) ? $packageInfo[2] : $domain;

        return [$resolve, $packagePath, $packageDomain, $packageDomainFilePrefix];
    }

    /**
     * Allow language overrides so for example de_AT uses de_DE to avoid duplicate
     * .po files and management. This is for JavaScript files!
     *
     * @param string $file
     * @param string $handle
     * @param string $domain
     * @return string
     */
    public function load_script_translation_file($file, $handle, $domain) {
        list($useFile, $packagePath, $packageDomain, $packageDomainFilePrefix) = $this->packageInfoParserForOverrides(
            self::$PACKAGE_INFO_FRONTEND,
            $file,
            $domain
        );

        $locale = determine_locale();
        if (
            $domain === $packageDomain &&
            !is_readable($useFile) &&
            substr($useFile, 0, strlen($packagePath)) === $packagePath
        ) {
            // Collect data
            $folder = dirname($useFile);
            $use = $this->override($locale);
            $wp_scripts = wp_scripts();
            $src = $wp_scripts->query($handle)->src;

            // Generate new file
            $file_base = $packageDomainFilePrefix . '-' . $use . '-' . md5(basename($src)) . '.json';
            return path_join($folder, $file_base);
        }

        return $file;
    }

    /**
     * Allow language overrides so for example de_AT uses de_DE to avoid duplicate
     * .po files and management. This is for backend PHP files!
     *
     * @param boolean $override
     * @param string $domain
     * @param string $mofile
     * @return boolean
     * @see https://webschale.de/2015/plugin-language-fallback-wenns-einem-die-sprache-verschlaegt/
     */
    public function override_load_textdomain($override, $domain, $mofile) {
        list($mofile, $packagePath, $packageDomain, $packageDomainFilePrefix) = $this->packageInfoParserForOverrides(
            self::$PACKAGE_INFO_BACKEND,
            $mofile,
            $domain
        );

        if (
            $domain === $packageDomain &&
            !is_readable($mofile) &&
            substr($mofile, 0, strlen($packagePath)) === $packagePath
        ) {
            $prefix = $packageDomainFilePrefix . '-';
            $folder = dirname($mofile);
            $locale = pathinfo(str_replace($prefix, '', basename($mofile)), PATHINFO_FILENAME);
            $use = $this->override($locale);

            $usemo = path_join($folder, $prefix . $use . '.mo');
            if (is_readable($usemo)) {
                load_textdomain($domain, $usemo);
                return true;
            }
        }
        return $override;
    }

    /**
     * Add filters to WordPress runtime.
     */
    public function hooks() {
        add_filter('override_load_textdomain', [$this, 'override_load_textdomain'], 10, 3);
        add_filter('load_script_translation_file', [$this, 'load_script_translation_file'], 10, 3);
    }

    /**
     * Resolves symlinks for a given file.
     *
     * @param string $path
     * @return string
     */
    public function resolveSymlinks($path) {
        return realpath(dirname($path)) . '/' . basename($path);
    }
}
