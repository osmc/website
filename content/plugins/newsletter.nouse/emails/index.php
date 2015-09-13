<?php
if (function_exists('load_plugin_textdomain')) {
    load_plugin_textdomain('newsletter-emails', false, 'newsletter/emails/languages');
    load_plugin_textdomain('newsletter', false, 'newsletter/languages');
}
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

if ($controls->is_action('convert')) {
    $module->convert_old_emails();
    $controls->messages = 'Converted!';
}

if ($controls->is_action('unconvert')) {
    $wpdb->query("update wp_newsletter_emails set type='email' where type='message'");
    $controls->messages = 'Unconverted!';
}

if ($controls->is_action('send')) {
    $newsletter->hook_newsletter();
    $controls->messages .= __('Delivery engine triggered.', 'newsletter-emails');
}

if ($controls->is_action('copy')) {
    $original = Newsletter::instance()->get_email($_POST['btn']);
    $email = array();
    $email['subject'] = $original->subject;
    $email['message'] = $original->message;
    $email['message_text'] = $original->message_text;
    $email['send_on'] = time();
    $email['type'] = 'message';
    $email['editor'] = $original->editor;
    $email['track'] = $original->track;

    Newsletter::instance()->save_email($email);
    $controls->messages .= __('Message duplicated.', 'newsletter-emails');
}

if ($controls->is_action('delete')) {
    Newsletter::instance()->delete_email($_POST['btn']);
    $controls->messages .= __('Message deleted.', 'newsletter-emails');
}

if ($controls->is_action('delete_selected')) {
    $r = Newsletter::instance()->delete_email($_POST['ids']);
    $controls->messages .= $r . ' message(s) deleted';
}

$emails = Newsletter::instance()->get_emails('message');
?>

<div class="wrap">

    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/newsletters-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

<div id="newsletter-title">
    <h2><?php _e('Newsletters', 'newsletter-emails')?></h2>

 </div>
    <div class="newsletter-separator"></div>
    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <?php if ($module->has_old_emails()) { ?>
            <div class="newsletter-message">
                <p>
                    Your Newsletter installation has emails still in old format. To get them listed, you should convert them in
                    a new format. Would you to convert them now?
                </p>
                <p>
                    <?php $controls->button('convert', 'Convert now'); ?>
                    <?php //$controls->button('unconvert', 'Unconvert (DEBUG)'); ?>
                </p>
            </div>
        <?php } ?>

        <p>
            <a href="<?php echo $module->get_admin_page_url('theme'); ?>" class="button"><?php _e('New newsletter', 'newsletter-emails')?></a>
            <?php $controls->button_confirm('delete_selected', __('Delete selected newsletters', 'newsletter-emails'), 
                    __('Proceed?', 'newsletter-emails')); ?>
            <?php $controls->button('send', __('Trigger the delivery engine', 'newsletter-emails')); ?>
        </p>
        <table class="widefat" style="width: auto">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Id</th>
                    <th><?php _e('Subject', 'newsletter')?></th>
                    <th><?php _e('Status', 'newsletter')?></th>
                    <th><?php _e('Progress', 'newsletter')?>&nbsp;(*)</th>
                    <th><?php _e('Date', 'newsletter')?></th>
                    <th><?php _e('Tracking', 'newsletter')?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($emails as &$email) { ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?php echo $email->id; ?>"/></td>
                        <td><?php echo $email->id; ?></td>
                        <td><?php echo htmlspecialchars($email->subject); ?></td>

                        <td>
                            <?php
                            if ($email->status == 'sending') {
                                if ($email->send_on > time()) {
                                    _e('Scheduled', 'newsletter-emails');
                                }
                                else {
                                    _e('Sending', 'newsletter-emails');
                                }
                            } else  {
                                echo $email->status;
                            }
                            ?>
                        </td>
                        <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $email->sent . ' ' . __('of', 'newsletter') . ' ' . $email->total; ?></td>
                        <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $module->format_date($email->send_on); ?></td>
                        <td><?php echo $email->track==1?__('Yes', 'newsletter-emails'):__('Yes', 'newsletter-emails'); ?></td>
                        <td><a class="button" href="<?php echo $module->get_admin_page_url('edit'); ?>&amp;id=<?php echo $email->id; ?>">Edit</a></td>
                        <td>
                            <a class="button" href="<?php echo NewsletterStatistics::instance()->get_statistics_url($email->id); ?>">Statistics</a>
                        </td>
                        <td><?php $controls->button_confirm('copy', __('Copy', 'newsletter-emails'), __('Proceed?', 'newsletter-emails'), $email->id); ?></td>
                        <td><?php $controls->button_confirm('delete', __('Delete', 'newsletter-emails'), __('Proceed?', 'newsletter-emails'), $email->id); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p>(*) <?php _e('The expected total can change at the delivery end due to subscriptions/unsubscriptions in the meanwhile.', 'newsletter-emails')?></p>
    </form>
</div>
