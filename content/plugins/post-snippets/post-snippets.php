<?php
/*
Plugin Name: Post Snippets
Plugin URI: http://johansteen.se/code/post-snippets/
Description: Build a library with snippets of HTML, PHP code or reoccurring text that you often use in your posts. Variables to replace parts of the snippet on insert can be used. The snippets can be inserted as-is or as shortcodes.
Author: Johan Steen
Author URI: http://johansteen.se/
Version: 2.3.6
License: GPLv2 or later
Text Domain: post-snippets

Copyright 2009-2015 Johan Steen  (email : artstorm [at] gmail [dot] com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** Load all of the necessary class files for the plugin */
spl_autoload_register('PostSnippets::autoload');

/**
 * Init Singleton Class.
 *
 * @author  Johan Steen <artstorm at gmail dot com>
 * @link    http://johansteen.se/
 */
class PostSnippets
{
    /** Holds the plugin instance */
    private static $instance = false;

    /** Define plugin constants */
    const MIN_PHP_VERSION     = '5.3.0';
    const MIN_WP_VERSION      = '3.3';
    const OPTION_KEY          = 'post_snippets_options';
    const USER_META_KEY       = 'post_snippets';
    const TEXT_DOMAIN         = 'post-snippets';
    const FILE                = __FILE__;

    /**
     * Singleton class
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initializes the plugin.
     */
    private function __construct()
    {
        if (!$this->testHost()) {
            return;
        }

        add_action('init', array($this, 'textDomain'));
        register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));

        add_action('after_setup_theme', array(&$this, 'phpExecState'));
        new PostSnippets_Admin;
        new PostSnippets_WPEditor;
        new PostSnippets_Shortcode;
    }

    /**
     * PSR-0 compliant autoloader to load classes as needed.
     *
     * @param  string  $classname  The name of the class
     * @return null    Return early if the class name does not start with the
     *                 correct prefix
     */
    public static function autoload($className)
    {
        if (__CLASS__ !== mb_substr($className, 0, strlen(__CLASS__))) {
            return;
        }
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            $fileName .= DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, 'src_'.$className);
        $fileName .='.php';

        require $fileName;
    }

    /**
     * Loads the plugin text domain for translation
     */
    public function textDomain()
    {
        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);
        load_textdomain(
            $domain,
            WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo'
        );
        load_plugin_textdomain(
            $domain,
            false,
            dirname(plugin_basename(__FILE__)).'/lang/'
        );
    }

    /**
     * Fired when the plugin is uninstalled.
     */
    public function uninstall()
    {
        // Delete all snippets
        delete_option('post_snippets_options');

        // Delete any per user settings
        global $wpdb;
        $wpdb->query(
            "
            DELETE FROM $wpdb->usermeta
            WHERE meta_key = 'post_snippets'
            "
        );
    }


    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Allow snippets to be retrieved directly from PHP.
     *
     * @since   Post Snippets 1.8.9.1
     *
     * @param  string  $name  The name of the snippet to retrieve
     * @param  string|array  $variables  The variables to pass to the snippet,
     *         formatted as a query string or an associative array.
     * @return string  The Snippet
     */
    public static function getSnippet($name, $variables = '')
    {
        $snippets = get_option(self::OPTION_KEY, array());
        for ($i = 0; $i < count($snippets); $i++) {
            if ($snippets[$i]['title'] == $name) {
                if (!is_array($variables)) {
                    parse_str(htmlspecialchars_decode($variables), $variables);
                }

                $snippet = $snippets[$i]['snippet'];
                $var_arr = explode(",", $snippets[$i]['vars']);

                if (!empty($var_arr[0])) {
                    for ($j = 0; $j < count($var_arr); $j++) {
                        $snippet = str_replace(
                            "{".$var_arr[$j]."}",
                            $variables[$var_arr[$j]],
                            $snippet
                        );
                    }
                }
                break;
            }
        }
        return do_shortcode($snippet);
    }

    // -------------------------------------------------------------------------
    // Environment Checks
    // -------------------------------------------------------------------------

    /**
     * Checks PHP and WordPress versions.
     */
    private function testHost()
    {
        // Check if PHP is too old
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            // Display notice
            add_action('admin_notices', array(&$this, 'phpVersionError'));
            return false;
        }

        // Check if WordPress is too old
        global $wp_version;
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            add_action('admin_notices', array(&$this, 'wpVersionError'));
            return false;
        }
        return true;
    }

    /**
     * Displays a warning when installed on an old PHP version.
     */
    public function phpVersionError()
    {
        echo '<div class="error"><p><strong>';
        printf(
            'Error: %3$s requires PHP version %1$s or greater.<br/>'.
            'Your installed PHP version: %2$s',
            self::MIN_PHP_VERSION,
            PHP_VERSION,
            $this->getPluginName()
        );
        echo '</strong></p></div>';
    }

    /**
     * Displays a warning when installed in an old Wordpress version.
     */
    public function wpVersionError()
    {
        echo '<div class="error"><p><strong>';
        printf(
            'Error: %2$s requires WordPress version %1$s or greater.',
            self::MIN_WP_VERSION,
            $this->getPluginName()
        );
        echo '</strong></p></div>';
    }

    /**
     * Get the name of this plugin.
     *
     * @return string The plugin name.
     */
    private function getPluginName()
    {
        $data = get_plugin_data(self::FILE);
        return $data['Name'];
    }

    // -------------------------------------------------------------------------
    // Deprecated methods
    // -------------------------------------------------------------------------
    /**
     * Allow plugins to disable the PHP Code execution feature with a filter.
     * Deprecated: Use the POST_SNIPPETS_DISABLE_PHP global constant to disable
     * PHP instead.
     *
     * @see   http://wordpress.org/extend/plugins/post-snippets/faq/
     * @since 2.1
     * @deprecated 2.3
     */
    public function phpExecState()
    {
        $filter = apply_filters('post_snippets_php_execution_enabled', true);
        if ($filter == false and !defined('POST_SNIPPETS_DISABLE_PHP')) {
            _deprecated_function(
                'post_snippets_php_execution_enabled',
                '2.3',
                'define(\'POST_SNIPPETS_DISABLE_PHP\', true);'
            );
            define('POST_SNIPPETS_DISABLE_PHP', true);
        }
    }
}

add_action('plugins_loaded', array('PostSnippets', 'getInstance'));
