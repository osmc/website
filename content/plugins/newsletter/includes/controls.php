<?php

class NewsletterControls {

    var $data;
    var $action = false;
    var $button_data = '';

    function __construct($options = null) {
        if ($options == null) {
            if (isset($_POST['options'])) {
                $this->data = stripslashes_deep($_POST['options']);
            }
        } else {
            $this->data = $options;
        }

        if (isset($_REQUEST['act'])) {
            $this->action = $_REQUEST['act'];
        }

        if (isset($_REQUEST['btn'])) {
            $this->button_data = $_REQUEST['btn'];
        }
        // Fields analysis
        if (isset($_REQUEST['fields'])) {
            $fields = $_REQUEST['fields'];
            if (is_array($fields)) {
                foreach ($fields as $name => $type) {
                    if ($type == 'datetime') {
                        // Ex. The user insert 01/07/2012 14:30 and it set the time zone to +2. We cannot use the
                        // mktime, since it uses the time zone of the machine. We create the time as if we are on
                        // GMT 0 and then we subtract the GMT offset (the example date and time on GMT+2 happens
                        // "before").

                        $time = gmmktime($_REQUEST[$name . '_hour'], 0, 0, $_REQUEST[$name . '_month'], $_REQUEST[$name . '_day'], $_REQUEST[$name . '_year']);
                        $time -= get_option('gmt_offset') * 3600;
                        $this->data[$name] = $time;
                    }
                }
            }
        }
    }

    function merge($options) {
        if (!is_array($options))
            return;
        if ($this->data == null)
            $this->data = array();
        $this->data = array_merge($this->data, $options);
    }

    function merge_defaults($defaults) {
        if ($this->data == null)
            $this->data = $defaults;
        else
            $this->data = array_merge($defaults, $this->data);
    }

    /**
     * Return true is there in an asked action is no action name is specified or
     * true is the requested action matches the passed action.
     * Dies if it is not a safe call.
     */
    function is_action($action = null) {
        if ($action == null)
            return $this->action != null;
        if ($this->action == null)
            return false;
        if ($this->action != $action)
            return false;
        if (check_admin_referer('save'))
            return true;
        die('Invalid call');
    }

    function get_value($name) {
        if (!isset($this->data[$name]))
            return null;
        return $this->data[$name];
    }

    function get_value_array($name) {
        if (!isset($this->data[$name]) || !is_array($this->data[$name]))
            return array();
        return $this->data[$name];
    }

    /**
     * Show the errors and messages.
     */
    function show() {
        if (!empty($this->errors)) {
            echo '<div class="error"><p>';
            echo $this->errors;
            echo '</p></div>';
        }
        if (!empty($this->messages)) {
            echo '<div class="updated"><p>';
            echo $this->messages;
            echo '</p></div>';
        }
    }

    function hint($text, $url = '') {
        echo '<div class="hints">';
        echo $text;
        if (!empty($url)) {
            echo " <a href='$url' target='_blank'>Read more</a>.";
        }
        echo '</div>';
    }

    function yesno($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 60px" name="options[' . $name . ']">';
        echo '<option value="0"';
        if ($value == 0)
            echo ' selected';
        echo '>No</option>';
        echo '<option value="1"';
        if ($value == 1)
            echo ' selected';
        echo '>Yes</option>';
        echo '</select>&nbsp;&nbsp;&nbsp;';
    }

    function enabled($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 100px" name="options[' . $name . ']">';
        echo '<option value="0"';
        if ($value == 0)
            echo ' selected';
        echo '>Disabled</option>';
        echo '<option value="1"';
        if ($value == 1)
            echo ' selected';
        echo '>Enabled</option>';
        echo '</select>';
    }

    /**
     * Creates a set of checkbox all named as $name with values and labels extracted from
     * $values_labels. A checkbox will be checked if internal data under key $name is an array
     * and contains the value of the current (echoing) checkbox.
     *
     * On submit it produces an array under the name $name IF at least one checkbox has
     * been checked. Otherwise the key won't be present.
     *
     * @param array $values
     * @param string $name
     * @param array $values_labels
     */
    function checkboxes_group($name, $values_labels) {
        $value_array = $this->get_value_array($name);

        echo "<div class='newsletter-checkboxes-group'>";
        foreach ($values_labels as $value => $label) {
            echo "<div class='newsletter-checkboxes-item'>";
            echo "<input type='checkbox' id='$name' name='options[$name][]' value='$value'";
            if (array_search($value, $value_array) !== false)
                echo " checked";
            echo '/>';
            if ($label != '')
                echo " <label for='$name'>$label</label>";
            echo "</div>";
        }
        echo "</div><div style='clear: both'></div>";
    }

    /** Creates a checkbox group with all public post types.
     */
    function post_types($name = 'post_types') {
        $list = array();
        $post_types = get_post_types(array('public' => true), 'objects', 'and');
        foreach ($post_types as &$post_type) {
            $list[$post_type->name] = $post_type->labels->name;
        }

        $this->checkboxes_group($name, $list);
    }

    /** Used to create a select which is part of a group of controls identified by $name that will
     * produce an array of values as $_REQUEST['name'].
     * @param string $name
     * @param array $options Associative array
     */
    function select_group($name, $options) {
        $value_array = $this->get_value_array($name);

        echo '<select name="options[' . $name . '][]">';

        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if (array_search($key, $value_array) !== false) {
                echo ' selected';
            }
            echo '>' . htmlspecialchars($label) . '</option>';
        }

        echo '</select>';
    }

    function select($name, $options, $first = null) {
        $value = $this->get_value($name);

        echo '<select id="options-' . $name . '" name="options[' . $name . ']">';
        if (!empty($first)) {
            echo '<option value="">' . htmlspecialchars($first) . '</option>';
        }
        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if ($value == $key)
                echo ' selected';
            echo '>' . htmlspecialchars($label) . '</option>';
        }
        echo '</select>';
    }

    function select_grouped($name, $groups) {
        $value = $this->get_value($name);

        echo '<select name="options[' . $name . ']">';

        foreach ($groups as $group) {
            echo '<optgroup label="' . htmlspecialchars($group['']) . '">';
            if (!empty($group)) {
                foreach ($group as $key => $label) {
                    if ($key == '')
                        continue;
                    echo '<option value="' . $key . '"';
                    if ($value == $key)
                        echo ' selected';
                    echo '>' . htmlspecialchars($label) . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>';
    }

    /**
     * Generated a select control with all available templates. From version 3 there are
     * only on kind of templates, they are no more separated by type.
     */
    function themes($name, $themes, $submit_on_click = true) {
        foreach ($themes as $key => $data) {
            echo '<label style="display: block; float: left; text-align: center; margin-right: 10px;">';
            echo $key . '<br>';
            echo '<img src="' . $data['screenshot'] . '" width="100" height="100" style="border: 1px solid #666; padding: 5px"><br>';
            echo '<input style="position: relative; top: -40px" type="radio" onchange="this.form.act.value=\'theme\';this.form.submit()" name="options[' . $name . ']" value="' . $key . '"';
            if ($this->data[$name] == $key) {
                echo ' checked';
            }
            echo '>';
            echo '</label>';
        }
        echo '<div style="clear: both"></div>';
    }

    function value($name) {
        echo htmlspecialchars($this->data[$name]);
    }

    function value_date($name, $show_remaining) {
        $time = $this->get_value($name);

        echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
        $delta = $time - time();
        if ($show_remaining && $delta > 0) {
            echo 'Remaining: ';
            $delta = $time - time();
            $days = floor($delta / (24 * 3600));
            $delta = $delta - $days * 24 * 3600;
            $hours = floor($delta / 3600);
            $delta = $delta - $hours * 3600;
            $minutes = floor($delta / 60);

            if ($days > 0)
                echo $days . ' days ';
            echo $hours . ' hours ';
            echo $minutes . ' minutes ';
        }
    }

    function text($name, $size = 20, $placeholder = '') {
        $value = $this->get_value($name);
        echo '<input placeholder="' . htmlspecialchars($placeholder) . '" name="options[' . $name . ']" type="text" size="' . $size . '" value="';
        echo htmlspecialchars($value);
        echo '"/>';
    }

    function text_email($name, $size = 40) {
        $value = $this->get_value($name);
        echo '<input name="options[' . $name . ']" type="email" placeholder="Valid email address" size="' . $size . '" value="';
        echo htmlspecialchars($value);
        echo '"/>';
    }

    function text_url($name, $size = 40) {
        $value = $this->get_value($name);
        echo '<input name="options[' . $name . ']" type="url" placeholder="http://..." size="' . $size . '" value="';
        echo htmlspecialchars($value);
        echo '"/>';
    }

    function hidden($name) {
        $value = $this->get_value($name);
        echo '<input name="options[' . $name . ']" type="hidden" value="';
        echo htmlspecialchars($value);
        echo '"/>';
    }

    function button($action, $label, $function = null) {
        if ($function != null) {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';' . htmlspecialchars($function) . '"/>';
        } else {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';this.form.submit()"/>';
        }
    }

    function button_primary($action, $label, $function = null) {
        if ($function != null) {
            echo '<input class="button-primary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';' . htmlspecialchars($function) . '"/>';
        } else {
            echo '<input class="button-primary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';this.form.submit()"/>';
        }
    }

    function button_confirm($action, $label, $message = '', $data = '') {
        if (empty($message)) {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.btn.value=\'' . $data . '\';this.form.act.value=\'' . $action . '\';this.form.submit()"/>';
        } else {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.btn.value=\'' . $data . '\';this.form.act.value=\'' . $action . '\';if (confirm(\'' .
            htmlspecialchars($message) . '\')) this.form.submit()"/>';
        }
    }

    function editor($name, $rows = 5, $cols = 75) {
        echo '<textarea class="visual" name="options[' . $name . ']" style="width: 100%" wrap="off" rows="' . $rows . '">';
        echo htmlspecialchars($this->get_value($name));
        echo '</textarea>';
    }

    function wp_editor($name, $settings = array()) {
        wp_editor($this->data[$name], $name, array_merge(array('textarea_name' => 'options[' . $name . ']', 'wpautop' => false), $settings));
        if (!is_plugin_active('mce_table_buttons/mce_table_buttons.php')) {
            echo '<p class="description">You can install <a href="https://wordpress.org/plugins/mce-table-buttons/" target="_blank">MCE Table Button</a> for a table management toolbar add on.</p>';
        }
    }

    function textarea($name, $width = '100%', $height = '50') {
        echo '<textarea class="dynamic" name="options[' . $name . ']" wrap="off" style="width:' . $width . ';height:' . $height . '">';
        echo htmlspecialchars($this->data[$name]);
        echo '</textarea>';
    }

    function textarea_fixed($name, $width = '100%', $height = '200') {
        echo '<textarea name="options[' . $name . ']" wrap="off" style="width:' . $width . ';height:' . $height . 'px">';
        echo htmlspecialchars($this->data[$name]);
        echo '</textarea>';
    }

    function email($prefix, $editor = null, $disable_option = false) {
        if ($disable_option) {
            $this->checkbox($prefix . '_disabled', 'Disable this email');
            echo '<br>';
        }
        //echo 'Subject:<br />';
        $this->text($prefix . '_subject', 90, 'Subject');
        //echo '<br />Message:<br />';
        if ($editor == 'wordpress') {
            $this->wp_editor($prefix . '_message');
        } else if ($editor == 'textarea') {
            $this->textarea($prefix . '_message');
        } else {
            $this->editor($prefix . '_message');
        }
    }

    function checkbox($name, $label = '') {
        if ($label != '')
            echo '<label>';
        echo '<input type="checkbox" id="' . $name . '" name="options[' . $name . ']" value="1"';
        if (!empty($this->data[$name]))
            echo ' checked="checked"';
        echo '/>';
        if ($label != '')
            echo '&nbsp;' . $label . '</label>';
    }

    /**
     * Creates a checkbox named $name and checked if the internal data contains under
     * the key $name an array containig the passed value.
     */
    function checkbox_group($name, $value, $label = '') {
        echo '<input type="checkbox" id="' . $name . '" name="options[' . $name . '][]" value="' . $value . '"';
        if (is_array($this->data[$name]) && array_search($value, $this->data[$name]) !== false)
            echo ' checked="checked"';
        echo '/>';
        if ($label != '')
            echo ' <label for="' . $name . '">' . $label . '</label>';
    }

    function color($name) {
        echo $this->text($name, 10);
    }

    /** Creates a set of checkbox named $name_[category id] (so they are posted with distinct names).
     */
    function categories($name = 'category') {
        $categories = get_categories();
        echo '<div class="newsletter-checkboxes-group">';
        foreach ($categories as &$c) {
            echo '<div class="newsletter-checkboxes-item">';
            $this->checkbox($name . '_' . $c->cat_ID, esc_html($c->cat_name));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
    }

    /**
     * Creates a set of checkbox to activate the profile preferences. Every checkbox has a DIV around to
     * be formatted.
     */
    function categories_group($name, $show_mode = false) {
        $categories = get_categories();
        if ($show_mode) {
            $this->select($name . '_mode', array('include' => 'To be included', 'exclude' => 'To be excluded'));
        }
        echo '<div class="newsletter-checkboxes-group">';
        foreach ($categories as &$c) {
            echo '<div class="newsletter-checkboxes-item">';
            $this->checkbox_group($name, $c->cat_ID, esc_html($c->cat_name));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
    }

    /**
     * Creates a set of checkboxes named $name_[preference number] (so they are
     * distinct fields).
     * Empty preferences are skipped.
     */
    function preferences($name = 'preferences') {
        $options_profile = get_option('newsletter_profile');
        echo '<div class="newsletter-preferences-group">';

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($options_profile['list_' . $i]))
                continue;
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox($name . '_' . $i, '<span class="newsletter-badge">' . $i . '</span> ' . htmlspecialchars($options_profile['list_' . $i]));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
        echo '<div class="hints">';
        echo 'User\'s preferences can be activated from the "Subscription Form" panel. They can be used to simulate lists or create private groups. The number is the "preference number". ';
        echo '<a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">Read more about preferences</a>.';
        echo '</div>';
    }

    /**
     * Creates a set of checkboxes all names $name[] and the preference number as value
     * so the selected checkboxes are retrieved as an array of values ($REQUEST[$name]
     * will be an array if at east one preference is checked).
     */
    function preferences_group($name = 'preferences') {
        $options_profile = get_option('newsletter_profile');

        echo '<div class="newsletter-preferences-group">';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($options_profile['list_' . $i]))
                continue;
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox_group($name, $i, '(' . $i . ') ' . htmlspecialchars($options_profile['list_' . $i]));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">'
        . 'Click here to read more about preferences.'
                . '</a> They can be configured on Subscription Form - Profile fields panel.';
        echo '</div>';
    }

    /** Creates as many selects as the active preferences with the three values
     * 'any', 'yes', 'no' corresponding to the values 0, 1, 2.
     */
    function preferences_selects($name = 'preferences', $skip_empty = false) {
        $options_profile = get_option('newsletter_profile');

        echo '<div class="newsletter-preferences-group">';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($options_profile['list_' . $i]))
                continue;

            echo '<div class="newsletter-preferences-item">';

            $this->select($name . '_' . $i, array(0 => 'Any', 1 => 'Yes', 2 => 'No'));
            echo '(' . $i . ') ' . htmlspecialchars($options_profile['list_' . $i]);

            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">Click here know more about preferences.</a> They can be configured on Subscription/Form field panel.';
        echo '</div>';
    }

    /** Creates a single select with the active preferences. */
    function preferences_select($name = 'preference') {
        $options_profile = get_option('newsletter_profile');

        $lists = array();
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
        }
        $this->select($name, $lists);
    }

    function date($name) {
        $this->hidden($name);
        $year = date('Y', $this->data[$name]);
        $day = date('j', $this->data[$name]);
        $month = date('m', $this->data[$name]);
        $onchange = "this.form.elements['options[" . $name . "]'].value = new Date(document.getElementById('" . $name . "_year').value, document.getElementById('" . $name . "_month').value, document.getElementById('" . $name . "_day').value, 12, 0, 0).getTime()/1000";
        echo '<select id="' . $name . '_month" onchange="' . $onchange . '">';
        for ($i = 0; $i < 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month - 1 == $i)
                echo ' selected';
            echo '>' . date('F', mktime(0, 0, 0, $i + 1, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select id="' . $name . '_day" onchange="' . $onchange . '">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select id="' . $name . '_year" onchange="' . $onchange . '">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';
    }

    function datetime($name) {
        echo '<input type="hidden" name="fields[' . $name . ']" value="datetime">';
        $time = $this->data[$name] + get_option('gmt_offset') * 3600;
        $year = gmdate('Y', $time);
        $day = gmdate('j', $time);
        $month = gmdate('m', $time);
        $hour = gmdate('H', $time);

        echo '<select name="' . $name . '_month">';
        for ($i = 1; $i <= 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month == $i)
                echo ' selected';
            echo '>' . date('F', mktime(0, 0, 0, $i, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_day">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_year">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_hour">';
        for ($i = 0; $i <= 23; $i++) {
            echo '<option value="' . $i . '"';
            if ($hour == $i)
                echo ' selected';
            echo '>' . $i . ':00</option>';
        }
        echo '</select>';
    }

    function hours($name) {
        $hours = array();
        for ($i = 0; $i < 24; $i++) {
            $hours['' . $i] = '' . $i;
        }
        $this->select($name, $hours);
    }

    function days($name) {
        $days = array(0 => 'Every day', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
        $this->select($name, $days);
    }

    function init($options = array()) {
        $cookie_name = 'newsletter_tab';
        if (isset($options['cookie_name'])) $cookie_name = $options['cookie_name'];
        echo '<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("textarea.dynamic").focus(function() {
            jQuery("textarea.dynamic").css("height", "50px");
            jQuery(this).css("height", "400px");
        });
      tabs = jQuery("#tabs").tabs({
        active : jQuery.cookie("' . $cookie_name . '"),
        activate : function( event, ui ){
            jQuery.cookie("' . $cookie_name . '", ui.newTab.index(),{expires: 1});
        }
      });
    });
    function newsletter_media(name) {
        var tnp_uploader = wp.media({
            title: "Select an image",
            button: {
                text: "Select"
            },
            multiple: false
        }).on("select", function() {
            var media = tnp_uploader.state().get("selection").first();
            document.getElementById(name + "_id").value = media.id;
            document.getElementById(name + "_url").value = media.attributes.url;
            document.getElementById(name + "_img").src = media.attributes.sizes.thumbnail.url;
        }).open();
    }
    function newsletter_media_remove(name) {
        if (confirm("Are you sure?")) {
            document.getElementById(name + "_id").value = "";
            document.getElementById(name + "_url").value = "";
            document.getElementById(name + "_img").src = "' . plugins_url('newsletter') . '/images/nomedia.png";
        }
    }
</script>
';
        echo '<input name="act" type="hidden" value=""/>';
        echo '<input name="btn" type="hidden" value=""/>';
        wp_nonce_field('save');
    }

    function log_level($name = 'log_level') {
        $this->select($name, array(0 => 'None', 2 => 'Error', 3 => 'Normal', 4 => 'Debug'));
    }

    function update_option($name, $data = null) {
        if ($data == null)
            $data = $this->data;
        update_option($name, $data);
        if (isset($data['log_level'])) {
            update_option($name . '_log_level', $data['log_level']);
        }
    }

//  function button_link($action, $url, $anchor) {
//    if (strpos($url, '?') !== false) $url .= $url . '&';
//    else $url .= $url . '?';
//    $url .= 'act=' . $action;
//
//    $url .= '&_wpnonce=' . wp_create_nonce();
//
//    echo '<a class="button" href="' . $url . '">' . $anchor . '</a>';
//  }

    function js_redirect($url) {
        echo '<script>';
        echo 'location.href="' . $url . '"';
        echo '</script>';
    }

    /**
     * @deprecated
     */
    function save_user($subscriber) {
        return NewsletterUsers::instance()->save_user($user);
    }

    /**
     * @deprecated
     */
    function get_test_subscribers() {
        return NewsletterUsers::instance()->get_test_users();
    }

    function css_font_size($name) {
        $value = $this->get_value($name);

        echo '<select id="options-' . $name . '" name="options[' . $name . ']">';
        for ($i = 8; $i < 50; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px';
    }

    function css_border($name) {
        $value = $this->get_value($name . '_width');

        echo 'width&nbsp;<select id="options-' . $name . '-width" name="options[' . $name . '_width]">';
        for ($i = 0; $i < 10; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px&nbsp;&nbsp;';

        $this->select($name . '_type', array('solid' => 'Solid', 'dashed' => 'Dashed'));

        $this->color($name . '_color');

        $value = $this->get_value($name . '_radius');

        echo '&nbsp;&nbsp;radius&nbsp;<select id="options-' . $name . '-radius" name="options[' . $name . '_radius]">';
        for ($i = 0; $i < 10; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i)
                echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px';
    }

    function media($name, $size='thumbnail') {
        $media_id = $this->data[$name]['id'];
        $media = wp_get_attachment_image_src($media_id, $size);

        if ($media === false) {
            $media = array('', '', '');
            echo '<img id="' . $name . '_img" src="' . plugins_url('newsletter') . '/images/nomedia.png" onclick="newsletter_media(\'' . $name . '\')">';
        } else {
            echo '<img id="' . $name . '_img" src="' . $media[0] . '" onclick="newsletter_media(\'' . $name . '\')">';
            echo '<br>';
            echo '<a href="#" onclick="newsletter_media_remove(\'' . $name . '\'); return false">Remove</a>';

        }

        echo '<input type="hidden" id="' . $name . '_id" name="options[' . $name . '][id]" value="' . $media_id . '" size="5">';
        echo '<input type="hidden" id="' . $name . '_url" name="options[' . $name . '][url]" value="' . esc_attr($media[0]) . '" size="50">';

    }

    function media_input($option, $name, $label) {

//        if (empty($option)) {
//            $option = $this->currentoption;
//        }
//        $options = $this->get_option($option);
//        $val = '';
//        if (isset($options[$var])) {
//            $val = $options[$var];
//        }

        if (!empty($label)) {
            $output = '<label class="select" for="tnp_' . $name . '">' . $label . ':</label>';
        }
        $output .= '<input id="tnp_' . $name . '" type="text" size="36" name="' . $option . '[' . $name . ']" value="' . esc_attr($val) . '" />';
        $output .= '<input id="tnp_' . $name . '_button" class="button" type="button" value="Select Image" />';
        $output .= '<br class="clear"/>';

        echo $output;
    }

}

?>
