<?php
/**
 * Post Snippets Settings.
 *
 * Class that renders out the HTML for the settings screen and contains helpful
 * methods to simply the maintainance of the admin screen.
 *
 * @author   Johan Steen <artstorm at gmail dot com>
 * @link     http://johansteen.se/
 */
class PostSnippets_Admin
{
    public function __construct()
    {
        add_filter('plugin_action_links', array(&$this, 'actionLinks'), 10, 2);
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('current_screen', array(&$this, 'addHeaderXss'));
    }


    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------

    /**
     * Quick link to the Post Snippets Settings page from the Plugins page.
     *
     * @param array Array of all plugin links
     * @param string The current plugin file we're filtering.
     * @return  Array with all the plugin's action links
     */
    public function actionLinks($links, $file)
    {
        $pluginFile = plugin_basename(dirname(PostSnippets::FILE));
        $pluginFile .= '/post-snippets.php';

        if ($file == $pluginFile) {
            $url = 'options-general.php?page=post-snippets/post-snippets.php';
            $link = "<a href='{$url}'>";
            $link .= __('Settings', PostSnippets::TEXT_DOMAIN).'</a>';
            $links[] = $link;
        }
        return $links;
    }

    /**
     * Initialize the administration page.
     */
    public function menu()
    {
        $capability = 'manage_options';
        if (defined('POST_SNIPPETS_ALLOW_EDIT_POSTS')
            and current_user_can('edit_posts')
        ) {
            $allowed = true;
            $capability = 'edit_posts';
        }

        if (current_user_can('manage_options') or isset($allowed)) {
            $optionPage = add_options_page(
                'Post Snippets Options',
                'Post Snippets',
                $capability,
                PostSnippets::FILE,
                array(&$this, 'optionsPage')
            );
            new PostSnippets_Help($optionPage);
        } else {
            $option_page = add_options_page(
                'Post Snippets',
                'Post Snippets',
                'edit_posts',
                PostSnippets::FILE,
                array(&$this, 'overviewPage')
            );
        }
    }

    /**
     * Add X-XSS-Protection header.
     *
     * Newer versions of Chrome does not allow form tags to be submitted in the
     * forms. This header disables that functionlity on the Post Snippets admin
     * screen only.
     */
    public function addHeaderXss($current_screen)
    {
        if ($current_screen->base == 'settings_page_post-snippets/post-snippets') {
            header('X-XSS-Protection: 0');
        }
    }


    // -------------------------------------------------------------------------
    // Handle form submissions
    // -------------------------------------------------------------------------

    /**
     * Add New Snippet.
     */
    private function add()
    {
        if (isset($_POST['add-snippet'])
            && isset($_POST['update_snippets_nonce'])
            && wp_verify_nonce($_POST['update_snippets_nonce'], 'update_snippets')
        ) {
            $snippets = get_option(PostSnippets::OPTION_KEY);
            if (empty($snippets)) {
                $snippets = array();
            }

            array_push(
                $snippets,
                array(
                    'title' => 'Untitled',
                    'vars' => '',
                    'description' => '',
                    'shortcode' => false,
                    'php' => false,
                    'wptexturize' => false,
                    'snippet' => ''
                )
            );

            update_option(PostSnippets::OPTION_KEY, $snippets);
            $this->message(
                __(
                    'A snippet named Untitled has been added.',
                    PostSnippets::TEXT_DOMAIN
                )
            );
        }
    }

    /**
     * Delete Snippet/s.
     */
    private function delete()
    {
        if (isset($_POST['delete-snippets'])
            && isset($_POST['update_snippets_nonce'])
            && wp_verify_nonce($_POST['update_snippets_nonce'], 'update_snippets')
        ) {
            $snippets = get_option(PostSnippets::OPTION_KEY);

            if (empty($snippets) || !isset($_POST['checked'])) {
                $this->message(
                    __('Nothing selected to delete.', PostSnippets::TEXT_DOMAIN)
                );
                return;
            }

            $delete = $_POST['checked'];
            $newsnippets = array();
            foreach ($snippets as $key => $snippet) {
                if (in_array($key, $delete) == false) {
                    array_push($newsnippets, $snippet);
                }
            }

            update_option(PostSnippets::OPTION_KEY, $newsnippets);
            $this->message(
                __(
                    'Selected snippets have been deleted.',
                    PostSnippets::TEXT_DOMAIN
                )
            );
        }
    }

    /**
     * Update Snippet/s.
     */
    private function update()
    {
        if (isset($_POST['update-snippets'])
            && isset($_POST['update_snippets_nonce'])
            && wp_verify_nonce($_POST['update_snippets_nonce'], 'update_snippets')
        ) {
            $snippets = get_option(PostSnippets::OPTION_KEY);
            if (!empty($snippets)) {
                foreach ($snippets as $key => $value) {
                    $new_snippets[$key]['title'] = trim($_POST[$key.'_title']);
                    $new_snippets[$key]['vars'] = str_replace(' ', '', trim($_POST[$key.'_vars']));
                    $new_snippets[$key]['shortcode'] = isset($_POST[$key.'_shortcode']) ? true : false;

                    if (!defined('POST_SNIPPETS_DISABLE_PHP')) {
                        $new_snippets[$key]['php'] = isset($_POST[$key.'_php']) ? true : false;
                    } else {
                        $new_snippets[$key]['php'] = isset($snippets[$key]['php']) ? $snippets[$key]['php'] : false;
                    }

                    $new_snippets[$key]['wptexturize'] = isset($_POST[$key.'_wptexturize']) ? true : false;

                    $new_snippets[$key]['snippet'] = wp_specialchars_decode(trim(stripslashes($_POST[$key.'_snippet'])), ENT_NOQUOTES);
                    $new_snippets[$key]['description'] = wp_specialchars_decode(trim(stripslashes($_POST[$key.'_description'])), ENT_NOQUOTES);
                }
                update_option(PostSnippets::OPTION_KEY, $new_snippets);
                $this->message(__('Snippets have been updated.', PostSnippets::TEXT_DOMAIN));
            }
        }
    }

    /**
     * Update User Option.
     *
     * Sets the per user option for the read-only overview page.
     *
     * @since   Post Snippets 1.9.7
     */
    private function setUserOptions()
    {
        if (isset($_POST['post_snippets_user_nonce'])
            && wp_verify_nonce($_POST['post_snippets_user_nonce'], 'post_snippets_user_options')
        ) {
            $id = get_current_user_id();
            $render = isset($_POST['render']) ? true : false;
            update_user_meta($id, PostSnippets::USER_META_KEY, $render);
        }
    }

    /**
     * Get User Option.
     *
     * Gets the per user option for the read-only overview page.
     *
     * @since   Post Snippets 1.9.7
     * @return  boolean If overview should be rendered on output or not
     */
    private function getUserOptions()
    {
        $id = get_current_user_id();
        $options = get_user_meta($id, PostSnippets::USER_META_KEY, true);
        return $options;
    }


    // -------------------------------------------------------------------------
    // HTML generation for option pages
    // -------------------------------------------------------------------------

    /**
     * Display Flashing Message.
     *
     * @param   string  $message    Message to display to the user.
     */
    private function message($message)
    {
        if ($message) {
            echo "<div class='updated'><p><strong>{$message}</strong></p></div>";
        }
    }

    /**
     * Creates the snippets administration page.
     *
     * For users with manage_options capability (admin, super admin).
     *
     * @since   Post Snippets 1.8.8
     */
    public function optionsPage()
    {
        // Handle Form Submits
        $this->add();
        $this->delete();
        $this->update();

        // Header
        echo '
        <!-- Create a header in the default WordPress \'wrap\' container -->
        <div class="wrap">
            <div id="icon-plugins" class="icon32"></div>
            <h2>Post Snippets</h2>
        ';

        // Tabs
        $active_tab = isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'snippets';
        $base_url = '?page=post-snippets/post-snippets.php&amp;tab=';
        $tabs = array('snippets' => __('Manage Snippets', PostSnippets::TEXT_DOMAIN), 'tools' => __('Import/Export', PostSnippets::TEXT_DOMAIN));
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $title) {
            $active = ($active_tab == $tab) ? ' nav-tab-active' : '';
            echo "<a href='{$base_url}{$tab}' class='nav-tab {$active}'>{$title}</a>";
        }
        echo '</h2>';
        echo '<p class="description">';
        _e('Use the help dropdown button for additional information.', PostSnippets::TEXT_DOMAIN);
        echo '</p>';

        // Tab content
        if ($active_tab == 'snippets') {
            $this->tabSnippets();
        } else {
            $this->tabTools();
        }

        // Close it
        echo '</div>';
    }

    /**
     * Tab to Manage Snippets.
     *
     * @since   Post Snippets 2.0
     */
    private function tabSnippets()
    {
        $data = array();
        echo PostSnippets_View::render('admin_snippets', $data);
    }

    /**
     * Tab for Import/Export
     *
     * @since   Post Snippets 2.0
     */
    private function tabTools()
    {
        $ie = new PostSnippets_ImportExport();

        // Create header and export html form
        printf("<h3>%s</h3>", __('Import/Export', PostSnippets::TEXT_DOMAIN));
        printf("<h4>%s</h4>", __('Export', PostSnippets::TEXT_DOMAIN));
        echo '<form method="post">';
        echo '<p>';
        _e('Export your snippets for backup or to import them on another site.', PostSnippets::TEXT_DOMAIN);
        echo '</p>';
        printf("<input type='submit' class='button' name='postsnippets_export' value='%s' />", __('Export Snippets', PostSnippets::TEXT_DOMAIN));
        echo '</form>';

        // Export logic, and import html form and logic
        $ie->exportSnippets();
        echo $ie->importSnippets();
    }


    /**
     * Creates a read-only overview page.
     *
     * For users with edit_posts capability but without manage_options
     * capability.
     *
     * @since   Post Snippets 1.9.7
     */
    public function overviewPage()
    {
        // Header
        echo '<div class="wrap">';
        echo '<h2>Post Snippets</h2>';
        echo '<p>';
        _e('This is an overview of all snippets defined for this site. These snippets are inserted into posts from the post editor using the Post Snippets button. You can choose to see the snippets here as-is or as they are actually rendered on the website. Enabling rendered snippets for this overview might look strange if the snippet have dependencies on variables, CSS or other parameters only available on the frontend. If that is the case it is recommended to keep this option disabled.', PostSnippets::TEXT_DOMAIN);
        echo '</p>';

        // Form
        $this->setUserOptions();
        $render = $this->getUserOptions();

        echo '<form method="post" action="">';
        wp_nonce_field('post_snippets_user_options', 'post_snippets_user_nonce');

        $this->checkbox(__('Display rendered snippets', PostSnippets::TEXT_DOMAIN), 'render', $render);
        $this->submit('update-post-snippets-user', __('Update', PostSnippets::TEXT_DOMAIN));
        echo '</form>';

        // Snippet List
        $snippets = get_option(PostSnippets::OPTION_KEY);
        if (!empty($snippets)) {
            foreach ($snippets as $key => $snippet) {
                echo "<hr style='border: none;border-top:1px dashed #aaa; margin:24px 0;' />";

                echo "<h3>{$snippet['title']}";
                if ($snippet['description']) {
                    echo "<span class='description'> {$snippet['description']}</span>";
                }
                echo "</h3>";

                if ($snippet['vars']) {
                    printf("<strong>%s:</strong> {$snippet['vars']}<br/>", __('Variables', PostSnippets::TEXT_DOMAIN));
                }

                // echo "<strong>Variables:</strong> {$snippet['vars']}<br/>";

                $options = array();
                if ($snippet['shortcode']) {
                    array_push($options, 'Shortcode');
                }
                if ($snippet['php']) {
                    array_push($options, 'PHP');
                }
                if ($snippet['wptexturize']) {
                    array_push($options, 'wptexturize');
                }
                if ($options) {
                    printf("<strong>%s:</strong> %s<br/>", __('Options', PostSnippets::TEXT_DOMAIN), implode(', ', $options));
                }

                printf("<br/><strong>%s:</strong><br/>", __('Snippet', PostSnippets::TEXT_DOMAIN));
                if ($render) {
                    echo do_shortcode($snippet['snippet']);
                } else {
                    echo "<code>";
                    echo nl2br(htmlspecialchars($snippet['snippet'], ENT_NOQUOTES));
                    echo "</code>";
                }
            }
        }
        // Close
        echo '</div>';
    }


    // -------------------------------------------------------------------------
    // HTML and Form element methods
    // -------------------------------------------------------------------------

    /**
     * Checkbox.
     *
     * Renders the HTML for an input checkbox.
     *
     * @param   string  $label      The label rendered to screen
     * @param   string  $name       The unique name and id to identify the input
     * @param   boolean $checked    If the input is checked or not
     */
    public static function checkbox($label, $name, $checked)
    {
        echo "<label for=\"{$name}\">";
        printf('<input type="checkbox" name="%1$s" id="%1$s" value="true"', $name);
        if ($checked) {
            echo ' checked';
        }
        echo ' />';
        echo " {$label}</label><br/>";
    }

    /**
     * Submit.
     *
     * Renders the HTML for a submit button.
     *
     * @since   Post Snippets 1.9.7
     * @param   string  $name   The name that identifies the button on submit
     * @param   string  $label  The label rendered on the button
     * @param   string  $class  Optional. Button class. Default: button-primary
     * @param   boolean $wrap   Optional. Wrap in a submit div. Default: true
     */
    public static function submit($name, $label, $class = 'button-primary', $wrap = true)
    {
        $btn = sprintf('<input type="submit" name="%s" value="%s" class="%s" />', $name, $label, $class);

        if ($wrap) {
            $btn = "<div class=\"submit\">{$btn}</div>";
        }

        echo $btn;
    }
}
