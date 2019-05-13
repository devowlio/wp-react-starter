<?php
namespace MatthiasWeb\WPRJSS\general;
use MatthiasWeb\WPRJSS\base;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * i18n management for your JavaScript frontend.
 */
class JsI18n extends base\Base {
    /**
     * This localization strings are loaded on backend and frontend widget.
     */
    private function common() {
        return [
            '_' => WPRJSS_TD // This is a must have because the frontend should handle the object as real object (not array)
        ];
    }

    /**
     * This localization strings are only loaded on backend.
     */
    private function backend() {
        return [
            'textDomainNotice' => __(
                'The text domain of the plugin is: %(textDomain)s (localized variable)',
                WPRJSS_TD
            ),
            'restUrlNotice' => __(
                'The WP REST API URL of the plugin is: {{a}}%(restUrl)s{{/a}} (localized variable)',
                WPRJSS_TD
            ),
            'infoNotice' => __('The is an informative notice', WPRJSS_TD),
            'successNotice' => __('Your action was successful', WPRJSS_TD),
            'errorNotice' => __('An unexpected error has occurred', WPRJSS_TD),
            'todoList' => __('Todo list', WPRJSS_TD),
            'todoListDescription' => __(
                'This section demonstrates a mobx-state-tree Todo list (no peristence to server).',
                WPRJSS_TD
            ),
            'remove' => __('Remove', WPRJSS_TD),
            'add' => __('Add', WPRJSS_TD),
            'todoInputPlaceholder' => __('What needs to be done?', WPRJSS_TD)
        ];
    }

    /**
     * This localization strings are only loaded on the frontend widget.
     */
    private function widget() {
        return [
            'widgetTitle' => __('Hello, World!', WPRJSS_TD),
            'widgetDescription' => __('I got generated from your new plugin!', WPRJSS_TD)
        ];
    }

    /**
     * Build the i18n with common and scenario-dependent localizations.
     *
     * @param string $context The context for the localized script
     * @return array
     */
    public function build($context) {
        $common = $this->common();

        if ($context === base\Assets::TYPE_ADMIN) {
            return array_merge($common, $this->backend());
        } else {
            return array_merge($common, $this->widget());
        }
    }
}
