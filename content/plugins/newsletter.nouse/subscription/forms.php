<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_forms');
}

if ($controls->is_action('save')) {
    update_option('newsletter_forms', $controls->data);
    $controls->messages = 'Saved.';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-forms'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <div id="newsletter-title">
        <?php include NEWSLETTER_DIR . '/subscription/menu.inc.php'; ?>

        <h2>Alternative Hand-Coded Forms</h2>
        <p>
            Here you can store your hand coded forms to recall them from short codes.
            <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-forms" target="_blank">Read more about forms</a>.
        </p>
    </div>
    <div class="newsletter-separator"></div>

    <?php $controls->show(); ?>
    <form method="post" action="">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-forms">Forms</a></li>
            </ul>

            <div id="tabs-forms">
                <table class="form-table">
                    <?php for ($i = 1; $i <= 10; $i++) { ?>
                        <tr valign="top">
                            <th>Form <?php echo $i; ?></th>
                            <td>
                                <?php $controls->textarea('form_' . $i); ?>
                                <br />
                                <?php $controls->button('save', 'Save'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>
    </form>

</div>