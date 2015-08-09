<?php

class NewsletterThemes {

    var $module;
    var $is_extension = false;

    function __construct($module, $is_extension = false) {
        $this->module = $module;
        $this->is_extension = $is_extension;
    }

    /** Loads all themes of a module (actually only "emails" module makes sense). Themes are located inside the subfolder
     * named as the module on plugin folder and on a subfolder named as the module on wp-content/newsletter folder (which
     * must be manually created).
     *
     * @param type $module
     * @return type
     */
    function get_all() {
        $list = array();

        $dir = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..')
                    continue;
                if (!is_file($dir . '/' . $file . '/theme.php'))
                    continue;
                $list[$file] = $file;
            }
            closedir($handle);
        }

        if (!$this->is_extension) {
            $dir = NEWSLETTER_DIR . '/' . $this->module . '/themes';
            $handle = @opendir($dir);

            if ($handle !== false) {
                while ($file = readdir($handle)) {
                    if ($file == '.' || $file == '..')
                        continue;
                    if (isset($list[$file]))
                        continue;
                    if (!is_file($dir . '/' . $file . '/theme.php'))
                        continue;

                    $list[$file] = $file;
                }
                closedir($handle);
            }
        }

        return $list;
    }
    
    function themescmp($a, $b)
    {
        $al = strtolower($a['name']);
        $bl = strtolower($b['name']);
        if ($al == 'default') {
            return -1;
        }
        return (strcmp($al, $bl));
    }

    function get_all_with_data() {
        $list = array();

        $dir = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..')
                    continue;
                if (isset($list[$file]))
                    continue;
                if (!is_file($dir . '/' . $file . '/theme.php'))
                    continue;
                $data = array();
                $data['name'] = $file;
                $screenshot = $dir . '/' . $file . '/screenshot.png';
                if (is_file($screenshot)) {
                    $data['screenshot'] = $this->get_theme_url($file) . '/screenshot.png';
                } else {
                    $data['screenshot'] = plugins_url('newsletter') . '/images/theme-screenshot.png';
                }
                $list[$file] = $data;
            }
            closedir($handle);
        }

        if (!$this->is_extension) {
            $dir = NEWSLETTER_DIR . '/' . $this->module . '/themes';
            $handle = @opendir($dir);

            if ($handle !== false) {
                while ($file = readdir($handle)) {
                    if ($file == '.' || $file == '..')
                        continue;
                    if (!is_file($dir . '/' . $file . '/theme.php'))
                        continue;
                    $data = array();
                    $data['name'] = $file;
                    $screenshot = $dir . '/' . $file . '/screenshot.png';
                    if (is_file($screenshot)) {
                        $data['screenshot'] = $this->get_theme_url($file) . '/screenshot.png';
                    } else {
                        $data['screenshot'] = plugins_url('newsletter') . '/images/theme-screenshot.png';
                    }
                    $list[$file] = $data;
                }
                closedir($handle);
            }
        }
        
        usort($list, array($this, "themescmp"));

        return $list;
    }

    /**
     *
     * @param type $theme
     * @param type $options
     * @param type $module
     */
    function save_options($theme, &$options) {
        add_option('newsletter_' . $this->module . '_theme_' . $theme, array(), null, 'no');
        $theme_options = array();
        foreach ($options as $key => &$value) {
            if (substr($key, 0, 6) != 'theme_')
                continue;
            $theme_options[$key] = $value;
        }
        update_option('newsletter_' . $this->module . '_theme_' . $theme, $theme_options);
    }

    function get_options($theme) {
        $options = get_option('newsletter_' . $this->module . '_theme_' . $theme);
        // To avoid merge problems.
        if (!is_array($options))
            return array();
        return $options;
    }

    function get_file_path($theme, $file) {
        $path = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes/' . $theme . '/' . $file;
        if (is_file($path))
            return $path;
        else
            return NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme . '/' . $file;
    }

    function get_theme_url($theme) {
        if ($this->is_extension) {
            return WP_CONTENT_URL . '/extensions/newsletter/' . $this->module . '/themes/' . $theme;
        }

        $path = NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme;
        if (is_dir($path)) {
            return plugins_url('newsletter') . '/' . $this->module . '/themes/' . $theme;
        } else {
            return WP_CONTENT_URL . '/extensions/newsletter/' . $this->module . '/themes/' . $theme;
        }
    }

    function get_default_options() {
        if ($this->is_extension) {
            $path2 = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes/' . $theme . '/languages';
            @include $path2 . '/en_US.php';
            @include $path2 . '/' . WPLANG . '.php';
        } else {
            $path1 = NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme . '/languages';
            $path2 = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes/' . $theme . '/languages';
            @include $path1 . '/en_US.php';
            @include $path2 . '/en_US.php';
            @include $path1 . '/' . WPLANG . '.php';
            @include $path2 . '/' . WPLANG . '.php';
        }

        if (!is_array($options))
            return array();
        return $options;
    }

}

function nt_option($name, $def = null) {
    $options = get_option('newsletter_email');
    $option = $options['theme_' . $name];
    if (!isset($option))
        return $def;
    else
        return $option;
}
