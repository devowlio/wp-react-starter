<?php
namespace MatthiasWeb\Utils;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base asset management class for frontend scripts and styles.
 */
trait Assets {
    /**
     * Enqueue scripts and styles in admin pages.
     */
    public static $TYPE_ADMIN = 'admin_enqueue_scripts';

    /**
     * Enqueue scripts and styles in frontend pages.
     */
    public static $TYPE_FRONTEND = 'wp_enqueue_scripts';

    /**
     * The regex to get the library folder name of public/lib files.
     */
    public static $LIB_CACHEBUSTER_REGEX = '/^public\/lib\/([^\/]+)/';

    public static $HANDLE_REACT = 'react';
    public static $HANDLE_REACT_DOM = 'react-dom';
    public static $HANDLE_MOBX = 'mobx';

    /**
     * Used in frontend localization to detect the i18n files.
     */
    public static $PUBLIC_JSON_I18N = 'public/languages/json';

    /**
     * Localize the plugin with additional options.
     *
     * @param string $context
     * @return array
     */
    abstract public function overrideLocalizeScript($context);

    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from public/lib, too.
     *
     * @param string $type The type (see Assets constants)
     */
    abstract public function enqueue_scripts_and_styles($type);

    /**
     * Localize the WordPress backend and frontend.
     *
     * @param string $context
     * @return mixed
     */
    public function localizeScript($context) {
        // We put custom variables to "others" because if you put for example
        // a boolean to the first-level it is interpreted as "1" instead of true.

        return [
            'slug' => $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_SLUG),
            'textDomain' => $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_TEXT_DOMAIN),
            'version' => $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION),
            'restUrl' => $this->getAsciiUrl(Service::getUrl($this)),
            'restNamespace' => Service::getNamespace($this),
            'restRoot' => $this->getAsciiUrl(get_rest_url()),
            'restQuery' => ['_v' => $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION)],
            'restNonce' => wp_installing() && !is_multisite() ? '' : wp_create_nonce('wp_rest'),
            'publicUrl' => trailingslashit(
                plugins_url('public', $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE))
            ),
            'others' => $this->overrideLocalizeScript($context)
        ];
    }

    /**
     * Enqueue react and react-dom (first check version is minium 16.8 so hooks work correctly).
     */
    public function enqueueReact() {
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        $reactDev = 'react/umd/react.development.js';
        $reactProd = 'react/umd/react.production.min.js';
        $reactDomDev = 'react-dom/umd/react-dom.development.js';
        $reactDomProd = 'react-dom/umd/react-dom.production.min.js';
        $enqueueDom = true;

        // React (first check version is minium 16.8 so hooks work correctly)
        $coreReact = wp_scripts()->query(self::$HANDLE_REACT);
        if ($coreReact !== false && version_compare($coreReact->ver, '16.8', '<')) {
            // Replace the already existing React version with ours
            $publicFolder = $this->getPublicFolder(true);
            $devFileExists = file_exists(
                $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH) . '/' . $publicFolder . $reactDev
            );
            $publicSrc = $publicFolder . ($useNonMinifiedSources && $devFileExists ? $reactDev : $reactProd);
            $coreReact->src = plugins_url($publicSrc, $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE));

            // Also use our ReactDOM, instead we get this error: https://reactjs.org/docs/error-decoder.html/?invariant=321
            $coreReactDom = wp_scripts()->query(self::$HANDLE_REACT_DOM);
            if ($coreReactDom !== false) {
                $publicSrc = $publicFolder . ($useNonMinifiedSources && $devFileExists ? $reactDomDev : $reactDomProd);
                $coreReactDom->src = plugins_url(
                    $publicSrc,
                    $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE)
                );
                $enqueueDom = false;
            }
        } else {
            // Enqueue our React version and let WP decide which version to take
            $this->enqueueLibraryScript(self::$HANDLE_REACT, [[$useNonMinifiedSources, $reactDev], $reactProd]);
        }

        if ($enqueueDom) {
            $this->enqueueLibraryScript(
                self::$HANDLE_REACT_DOM,
                [[$useNonMinifiedSources, $reactDomDev], $reactDomProd],
                self::$HANDLE_REACT
            );
        }
    }

    /**
     * Enqueue mobx state management library.
     */
    public function enqueueMobx() {
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        $this->enqueueLibraryScript(self::$HANDLE_MOBX, [
            [$useNonMinifiedSources, 'mobx/lib/mobx.umd.js'],
            'mobx/lib/mobx.umd.min.js'
        ]);
    }

    /**
     * Checks if a `vendor~` file is created for a given script and enqueue it.
     *
     * @param string $handle
     * @param boolean $isLib
     * @param string $src
     * @param string[] $deps
     * @param boolean $in_footer
     * @param string $media
     */
    protected function probablyEnqueueChunk($handle, $isLib, $src, &$deps, $in_footer, $media) {
        if (!$isLib) {
            $handle = $this->enqueue('vendor~' . $handle, 'vendor~' . $src, $deps, false, 'script', $in_footer, $media);
            if ($handle !== false) {
                array_push($deps, $handle);
            }
        }
    }

    /**
     * Enqueue helper for entry points and libraries. See dependents for more documentation.
     *
     * @param string $handle
     * @param mixed $src
     * @param string[] $deps
     * @param boolean $isLib
     * @param string $type Can be 'script' or 'style'
     * @param boolean $in_footer
     * @param string $media
     * @return string|boolean The used handle
     */
    protected function enqueue(
        $handle,
        $src,
        $deps = [],
        $isLib = false,
        $type = 'script',
        $in_footer = true,
        $media = 'all'
    ) {
        $useHandle = $isLib ? $handle : $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_SLUG) . '-' . $handle;
        if (!is_array($src)) {
            $src = [$src];
        }

        $publicFolder = $this->getPublicFolder($isLib);
        foreach ($src as $s) {
            // Allow to skip e. g. SCRIPT_DEBUG files
            if (is_array($s) && $s[0] !== true) {
                continue;
            }

            $useSrc = (is_array($s) ? $s[1] : $s);
            $publicSrc = $publicFolder . $useSrc;
            $path = path_join($this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH), $publicSrc);
            if (file_exists($path)) {
                $url = plugins_url($publicSrc, $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE));
                $cachebuster = $this->getCachebusterVersion($publicSrc, $isLib);

                if ($type === 'script') {
                    $this->probablyEnqueueChunk($useHandle, $isLib, $useSrc, $deps, $in_footer, $media);
                    wp_enqueue_script($useHandle, $url, $deps, $cachebuster, $in_footer);

                    // Only set translations for our own entry points, libraries handle localization usually in another way
                    if (!$isLib) {
                        $this->setLazyScriptTranslations(
                            $useHandle,
                            $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_TEXT_DOMAIN),
                            path_join(
                                $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH),
                                self::$PUBLIC_JSON_I18N
                            )
                        );
                    }
                } else {
                    wp_enqueue_style($useHandle, $url, $deps, $cachebuster, $media);
                }
                return $useHandle;
            }
        }

        return false;
    }

    /**
     * Registers the script if $src provided (does NOT overwrite), and enqueues it. Use this wrapper
     * method instead of wp_enqueue_script if you want to use the cachebuster for the given src. If the
     * src is not found in the cachebuster (inc/base/others/cachebuster.php) it falls back to _VERSION.
     *
     * You can also use something like this to determine SCRIPT_DEBUG files:
     *
     * ```php
     * $this->enqueueLibraryScript(
     *     Assets::$HANDLE_REACT_DOM,
     *     [[$useNonMinifiedSources, 'react-dom/umd/react-dom.development.js'], 'react-dom/umd/react-dom.production.min.js'],
     *     Assets::$HANDLE_REACT
     * );
     * ```
     *
     * @param string $handle Name of the script. Should be unique.
     * @param mixed $src The src relative to public/dist or public/dev folder (when $isLib is false)
     * @param string[] $deps An array of registered script handles this script depends on.
     * @param boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @return string|boolean The used handle
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/ For parameters
     */
    public function enqueueScript($handle, $src, $deps = [], $in_footer = true, $isLib = false) {
        return $this->enqueue($handle, $src, $deps, $isLib, 'script', $in_footer);
    }

    /**
     * Wrapper for Assets#enqueueScript() method with $isLib = true.
     *
     * @param string $handle
     * @param mixed $src
     * @param string[] $deps
     * @param boolean $in_footer
     * @return string|boolean The used handle
     * @see self::enqueueScript()
     */
    public function enqueueLibraryScript($handle, $src, $deps = [], $in_footer = false) {
        return $this->enqueueScript($handle, $src, $deps, $in_footer, true);
    }

    /**
     * Enqueue a CSS stylesheet. Use this wrapper method instead of wp_enqueue_style if you want
     * to use the cachebuster for the given src. If the src is not found in the cachebuster (inc/base/others/cachebuster.php)
     * it falls back to _VERSION.
     *
     * It also allows $src to be like in enqueueScript()
     *
     * @param string $handle Name of the style. Should be unique.
     * @param mixed $src The src relative to public/dist or public/dev folder (when $isLib is false)
     * @param string[] $deps An array of registered style handles this style depends on.
     * @param string $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @return string|boolean The used handle
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/ For parameters
     */
    public function enqueueStyle($handle, $src, $deps = [], $media = 'all', $isLib = false) {
        return $this->enqueue($handle, $src, $deps, $isLib, 'style', null, $media);
    }

    /**
     * Wrapper for Assets#enqueueStyle() method with $isLib = true.
     *
     * @param string $handle
     * @param mixed $src
     * @param string[] $deps
     * @param string $media
     * @return string|boolean The used handle
     * @see enqueueStyle()
     */
    public function enqueueLibraryStyle($handle, $src, $deps = [], $media = 'all') {
        return $this->enqueueStyle($handle, $src, $deps, $media, true);
    }

    /**
     * Checks if a `vendor~` file is created for a given script in a composer package and enqueue it.
     *
     * @param string $handle
     * @param string $src
     * @param string[] $deps
     * @param boolean $in_footer
     * @param string $media
     */
    protected function probablyEnqueueComposerChunk($handle, $src, &$deps, $in_footer, $media) {
        $rootSlug = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_ROOT_SLUG);
        $handle = $this->enqueueComposer($handle, 'vendor~' . $src, $deps, 'script', $in_footer, $media, 'vendor~' . $rootSlug . '-' . $handle);
        if ($handle !== false) {
            array_push($deps, $handle);
        }
    }

    /**
     * Enqueue helper for monorepo packages. See dependents for more documentation.
     *
     * @param string $handle
     * @param string $src
     * @param string[] $deps
     * @param string $type Can be 'script' or 'style'
     * @param boolean $in_footer
     * @param string $media
     * @param string $vendorHandle
     * @return string|boolean The used handle
     */
    protected function enqueueComposer(
        $handle,
        $src = 'index.js',
        $deps = [],
        $type = 'script',
        $in_footer = true,
        $media = 'all',
        $vendorHandle = null
    ) {
        $rootSlug = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_ROOT_SLUG);
        $useHandle = $vendorHandle !== null ? $vendorHandle : $rootSlug . '-' . $handle;
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        $packageDir = 'vendor/' . $rootSlug . '/' . $handle . '/';
        $packageSrc = $packageDir . ($useNonMinifiedSources ? 'dev' : 'dist') . '/' . $src;
        $pluginPath = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH);
        $composerPath = path_join($pluginPath, $packageSrc);
        $isInLernaRepo = file_exists(path_join(WP_CONTENT_DIR, 'packages/' . $handle . '/tsconfig.json'));

        if (file_exists($composerPath)) {
            // The lerna package exists (we are in our local development environment!)
            $url = plugins_url($packageSrc, $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE));
            $cachebuster = filemtime($composerPath);
            $packageJson = path_join($pluginPath, $packageDir . 'package.json');

            // Correct cachebuster version
            if (file_exists($packageJson) && !$isInLernaRepo) {
                $packageJson = json_decode(file_get_contents($packageJson), true);
                $cachebuster = $packageJson['version'];
            }

            if ($type === 'script') {
                $this->probablyEnqueueComposerChunk($handle, $src, $deps, $in_footer, $media);
                wp_enqueue_script($useHandle, $url, $deps, $cachebuster, $in_footer);
                $this->setLazyScriptTranslations(
                    $useHandle,
                    $useHandle,
                    path_join($pluginPath, $packageDir . 'languages/frontend/json')
                );
            } else {
                wp_enqueue_style($useHandle, $url, $deps, $cachebuster, $media);
            }
            return $useHandle;
        }

        return false;
    }

    /**
     * Enqueue a composer package script from our multi-package repository.
     *
     * @param string $handle Name of the package.
     * @param string[] $deps An array of registered scripts handles this script depends on.
     * @param string $src The file to use in dist or dev folder.
     * @param boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     * @return string The used handle
     */
    public function enqueueComposerScript($handle, $deps = [], $src = 'index.js', $in_footer = true) {
        return $this->enqueueComposer($handle, $src, $deps, 'script', $in_footer);
    }

    /**
     * Enqueue a composer package style from our multi-package repository.
     *
     * @param string $handle Name of the package.
     * @param string[] $deps An array of registered scripts handles this script depends on.
     * @param string $src The file to use in dist or dev folder.
     * @param string $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @return string The used handle
     */
    public function enqueueComposerStyle($handle, $deps = [], $src = 'index.css', $media = 'all') {
        return $this->enqueueComposer($handle, $src, $deps, 'style', null, $media);
    }

    /**
     * Enqueue scripts and styles for admin pages.
     */
    public function admin_enqueue_scripts() {
        $this->enqueue_scripts_and_styles(self::$TYPE_ADMIN);
    }

    /**
     * Enqueue scripts and styles for frontend pages.
     */
    public function wp_enqueue_scripts() {
        $this->enqueue_scripts_and_styles(self::$TYPE_FRONTEND);
    }

    /**
     * The function and mechanism of wp_set_script_translations() is great of course. Unfortunately
     * popular plugins like WP Rocket and Divi are not compatible with it (especially page builders
     * and caching plugins). Why? Shortly explained, the injected inline scripts relies on `wp.i18n`
     * which can be deferred or async loaded (the script itself) -> wp is not defined.
     *
     * In factory i18n.tsx the `window.wpi18nLazy` is automatically detected and the plugin gets localized.
     *
     * @param string $handle
     * @param string $domain
     * @param string $path
     * @see https://developer.wordpress.org/reference/classes/wp_scripts/print_translations/
     * @see https://developer.wordpress.org/reference/functions/wp_set_script_translations/
     * @see https://app.clickup.com/t/3mjh0e
     */
    public function setLazyScriptTranslations($handle, $domain, $path) {
        $json_translations = load_script_textdomain($handle, $domain, $path);
        if (!empty($json_translations)) {
            $output = <<<JS
(function(domain, translations) {
    var localeData = translations.locale_data[domain] || translations.locale_data.messages;
    localeData[""].domain = domain;
    window.wpi18nLazy=window.wpi18nLazy || {};
    window.wpi18nLazy[domain] = window.wpi18nLazy[domain] || [];
    window.wpi18nLazy[domain].push(localeData);
})("{$domain}", {$json_translations});
JS;
            wp_add_inline_script($handle, $output, 'before');
        }
    }

    /**
     * Get the cachebuster entry for a given file. If the $src begins with public/lib/ it
     * will use the inc/base/others/cachebuster-lib.php cachebuster instead of inc/base/others/cachebuster.php.
     *
     * @param string $src The src relative to public/ folder
     * @param boolean $isLib If true the cachebuster-lib.php cachebuster is used
     * @return string _VERSION or cachebuster timestamp
     */
    public function getCachebusterVersion($src, $isLib = false) {
        $default = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION);
        $path = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_INC) . '/base/others/';
        $path_lib = $path . 'cachebuster-lib.php';
        $path = $path . 'cachebuster.php';
        if ($isLib) {
            // Library cachebuster
            if (file_exists($path_lib)) {
                static $cachebuster_lib = null;
                if ($cachebuster_lib === null) {
                    $cachebuster_lib = include $path_lib;
                }

                // Parse module
                preg_match(self::$LIB_CACHEBUSTER_REGEX, $src, $matches);
                if (
                    is_array($matches) &&
                    isset($matches[1]) &&
                    ($module = $matches[1]) &&
                    is_array($cachebuster_lib) &&
                    array_key_exists($module, $cachebuster_lib)
                ) {
                    // Valid cachebuster
                    return $cachebuster_lib[$module];
                }
            }
        } else {
            // Main cachebuster
            if (file_exists($path)) {
                // Store cachebuster once
                static $cachebuster = null;
                if ($cachebuster === null) {
                    $cachebuster = include $path;
                }

                // Prepend src/ because the Grunt cachebuster prefixes it
                $src = 'src/' . $src;
                if (is_array($cachebuster) && array_key_exists($src, $cachebuster)) {
                    // Valid cachebuster
                    return $cachebuster[$src];
                }
            }
        }
        return $default;
    }

    /**
     * Wrapper for plugins_url. It respects the public folder depending on the SCRIPTS_DEBUG constant.
     *
     * @param string $asset The file name relative to the public folder path (dist or dev)
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @return string
     * @see getPublicFolder()
     */
    public function getPluginsUrl($asset, $isLib = false) {
        return plugins_url(
            $this->getPublicFolder($isLib) . $asset,
            $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE)
        );
    }

    /**
     * Gets a public folder depending on the debug mode relative to the plugins folder with trailing slash.
     *
     * @param boolean $isLib If true the public/lib/ folder is returned.
     * @return string
     */
    public function getPublicFolder($isLib = false) {
        return 'public/' . ($isLib ? 'lib' : ($this->useNonMinifiedSources() ? 'dev' : 'dist')) . '/';
    }

    /**
     * Convert a complete URL to IDN url. This is necessery if you use a URIBuilder like
     * lil-url in your frontend.
     *
     * @see https://www.php.net/manual/en/function.idn-to-ascii.php
     * @param string $url The url
     * @return string
     * @codeCoverageIgnore Completely relies on external library!
     */
    public function getAsciiUrl($url) {
        if (!class_exists('Requests_IRI')) {
            require_once ABSPATH . WPINC . '/Requests/IRI.php';
        }
        if (!class_exists('Requests_IDNAEncoder')) {
            require_once ABSPATH . WPINC . '/Requests/IDNAEncoder.php';
        }
        $iri = new \Requests_IRI($url);
        $iri->host = \Requests_IDNAEncoder::encode($iri->host);
        return $iri->uri;
    }

    /**
     * Check if SCRIPT_DEBUG is set to true.
     *
     * @return boolean
     */
    public function useNonMinifiedSources() {
        return defined('SCRIPT_DEBUG') && constant('SCRIPT_DEBUG') === true;
    }

    /**
     * Checks if a specific screen is active.
     *
     * @param string $base The base
     * @return boolean
     */
    public function isScreenBase($base) {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
        } else {
            return false;
        }

        if (isset($screen->base)) {
            return $screen->base === $base;
        } else {
            return false;
        }
    }
}
