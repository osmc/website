<?php
if (function_exists('load_plugin_textdomain')) {
    load_plugin_textdomain('newsletter-statistics', false, 'newsletter/statistics/languages');
    load_plugin_textdomain('newsletter', false, 'newsletter/languages');
}
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();
$emails = Newsletter::instance()->get_emails();

if (!$controls->is_action()) {
    $controls->data = $module->options;
}

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    $controls->messages = 'Saved.';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/statistics-module'; ?>

    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <div id="newsletter-title">
        <h2>Configuration and Email List</h2>

        <p>
            This module is a core part of Newsletter that collects statistics about sent emails: how many have
            been read, how many have been clicked and so on.
        </p>
        <p>
            To see the statistics of each single email, you should click the "statistics" button
            you will find near each message where they are listed (like on Newsletters panel). For your
            convenience, below there is a list of each email sent by Newsletter till now.
        </p>
        <p>
            <strong>Advanced reports for each email can be generated installing the
            <a href="http://www.thenewsletterplugin.com/plugins/newsletter/reports-module?utm_source=plugin&utm_medium=link&utm_campaign=newsletter-report&utm_content=<?php echo NEWSLETTER_VERSION?>" target="_blank">Reports Extension</a></strong>.
        </p>
    </div>
    <div class="newsletter-separator"></div>
    
    <form method="post" action="">
        <?php $controls->init(); ?>
    <table class="form-table">
        <tr>
            <th><?php _e('Tracking URL', 'newsletter-statistics') ?></th>
            <td>
                <?php $controls->select('tracking_url', array(0=>__('Standard', 'newsletter-statistics'), 
                    1=>__('Blog Home URL with parameters', 'newsletter-statistics'))) ?>
                <p class="description">
                    <?php _e('How the links inside newsletters are rewritten to track clicks.', 'newsletter-statistics') ?> 
                    <?php _e('Since spam filters check links inside emails, <a href="http://www.thenewsletterplugin.com/plugins/newsletter/statistics-module#tracking-url" target="_blank">read more about this setting</a>.', 'newsletter-statistics') ?> 
                </p>
            </td>
        </tr>
        <tr>
            <th><?php _e('Secret key', 'newsletter-statistics') ?></th>
            <td>
                <?php $controls->value('key') ?>
                <p class="description">
                    <?php _e('This auto-generated key is used to protect the click tracking. Shown for debug purposes.', 'newsletter-statistics') ?> 
                </p>
            </td>
        </tr>        
    </table>
    <p>
        <?php $controls->button('save', __('Save', 'newsletter')) ?>
    </p>
    </form>

    <table class="widefat" style="width: auto">
        <thead>
            <tr>
                <th>Id</th>
                <th><?php _e('Subject', 'newsletter')?></th>
                <th>Type</th>
                <th><?php _e('Status', 'newsletter')?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th><?php _e('Tracking', 'newsletter')?></th>
                <th>&nbsp;</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($emails as &$email) { ?>
            <?php if ($email->type != 'message' && $email->type != 'feed') continue; ?>
                <tr>
                    <td><?php echo $email->id; ?></td>
                    <td><?php echo htmlspecialchars($email->subject); ?></td>
                    <td><?php echo $email->type; ?></td>
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
                    <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $email->sent . ' ' . __('of', 'newsletter'). ' ' . $email->total; ?></td>
                    <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $module->format_date($email->send_on); ?></td>
                    <td><?php echo $email->track==1?'Yes':'No'; ?></td>
                    <td>
                        <a class="button" href="<?php echo NewsletterStatistics::instance()->get_statistics_url($email->id); ?>">statistics</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
