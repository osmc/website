<?php
if (function_exists('load_plugin_textdomain')) {
    load_plugin_textdomain('newsletter-emails', false, 'newsletter/emails/languages');
    load_plugin_textdomain('newsletter', false, 'newsletter/languages');
}
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

if ($controls->is_action('theme')) {
    $controls->merge($module->themes->get_options($controls->data['theme']));
    $module->save_options($controls->data);

    // If this theme has no intermediate options...
    if (!file_exists($module->get_current_theme_file_path('theme-options.php'))) {
        $email = array();
        $email['status'] = 'new';
        $email['subject'] = __('Here the email subject', 'newsletter-emails');
        $email['track'] = 1;

        $theme_options = $module->get_current_theme_options();
        $theme_url = $module->get_current_theme_url();
        $theme_subject = '';

        ob_start();
        include $module->get_current_theme_file_path('theme.php');
        $email['message'] = ob_get_clean();

        if (!empty($theme_subject)) {
            $email['subject'] = $theme_subject;
        }

        ob_start();
        include $module->get_current_theme_file_path('theme-text.php');
        $email['message_text'] = ob_get_clean();

        $email['type'] = 'message';
        $email['send_on'] = time();
        $email = Newsletter::instance()->save_email($email);
        ?>
        <script>
            location.href = "<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>";
        </script>
        <div class="wrap">
            <p>If you are not automatically redirected to the composer, <a href="<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>">click here</a>.</p>
        </div>
        <?php
        return;
    }
}

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    //$controls->messages = 'Saved.';
}

if ($controls->is_action('create')) {
    $module->save_options($controls->data);

    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter-emails');
    $email['track'] = 1;

    $theme_options = $module->get_current_theme_options();

    $theme_url = $module->get_current_theme_url();
    $theme_subject = '';

    ob_start();
    include $module->get_current_theme_file_path('theme.php');
    $email['message'] = ob_get_clean();

    if (!empty($theme_subject)) {
        $email['subject'] = $theme_subject;
    }

    ob_start();
    include $module->get_current_theme_file_path('theme-text.php');
    $email['message_text'] = ob_get_clean();

    $email['type'] = 'message';
    $email['send_on'] = time();
    $email = Newsletter::instance()->save_email($email);
    ?>
    <script>
        location.href = "<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>";
    </script>
    <div class="wrap">
        <p><a href="<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>">click here to proceed</a>.</p>
    </div>
    <?php
    return;
}

if ($controls->data == null) {
    $controls->data = $module->get_options();
}

function newsletter_emails_update_options($options) {
    add_option('newsletter_emails', '', null, 'no');
    update_option('newsletter_emails', $options);
}

function newsletter_emails_update_theme_options($theme, $options) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    add_option('newsletter_emails_' . $theme, '', null, 'no');
    update_option('newsletter_emails_' . $theme, $options);
}

function newsletter_emails_get_options() {
    $options = get_option('newsletter_emails', array());
    return $options;
}

function newsletter_emails_get_theme_options($theme) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    $options = get_option('newsletter_emails_' . $theme, array());
    return $options;
}
?>

<div class="wrap">

    <?php //$help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/newsletters-module';  ?>
    <?php //include NEWSLETTER_DIR . '/header-new.php';  ?>

    <div id="newsletter-title">
        <h2><?php _e('Create a newsletter', 'newsletter-emails') ?>
            <a class="add-new-h2" href="<?php echo NewsletterEmails::instance()->get_admin_page_url('theme'); ?>"><?php _e('Back to the themes', 'newsletter-emails') ?></a>
        </h2>
    </div>
    <div class="newsletter-separator"></div>

    <?php $controls->show(); ?>

    <form method="post" action="<?php echo $module->get_admin_page_url('new'); ?>">
        <?php $controls->init(); ?>
        <?php $controls->hidden('theme'); ?>

        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                    <div style="float: right; margin-left: 15px;"><?php $controls->button_primary('save', __('Refresh the preview', 'newsletter-emails')); ?></div>
                    <span style="font-size: 1.1em"><?php _e('Theme options are saved for next time you\'ll use this theme.', 'newsletter-emails') ?></span>

                </td>
                <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                    <div style="float: right"><?php $controls->button_primary('create', 'Proceed to edit &raquo;', 'this.form.action=\'' . plugins_url('newsletter') . '/emails/create.php\';this.form.submit()'); ?></div>
                    <img style="position: relative; left: 5px; top: 10px;"src="<?php echo plugins_url('newsletter') ?>/images/arrow.png" height="35">
                </td>
            </tr>
            <tr>
                <td style="width: 600px; vertical-align: top; padding-top: 10px">
                    <?php @include $module->get_current_theme_file_path('theme-options.php'); ?>
                </td>
                <td style="vertical-align: top; padding-top: 15px; padding-left: 15px">
                    <iframe src="<?php echo wp_nonce_url(plugins_url('newsletter') . '/emails/preview.php?' . time()); ?>" width="100%" height="700" style="border: 1px solid #ccc"></iframe>
                </td>
            </tr>
        </table>

    </form>
</div>
