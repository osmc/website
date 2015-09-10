<?php
global $current_user;

$dismissed = get_option('newsletter_dismissed', array());

if (isset($_REQUEST['dismiss'])) {
    $dismissed[$_REQUEST['dismiss']] = 1;
    update_option('newsletter_dismissed', $dismissed);
}

$user_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'");

?>

<?php if (NEWSLETTER_HEADER) { ?>
    <div id="newsletter-header-new">
        <!--
            <a href="http://www.thenewsletterplugin.com" target="_blank"><img src="<?php echo plugins_url('newsletter'); ?>/images/header/logo.png" style="height: 30px; margin-bottom: 10px; display: block;"></a>

            <div style="border-top: 1px solid white; width: 100%; margin-bottom: 10px;"></div>
        -->
        <?php if (NEWSLETTER_DEBUG) { ?>
        <img src="<?php echo plugins_url('newsletter'); ?>/images/header/debug.png" style="vertical-align: middle;" title="Debug mode active!">&nbsp;&nbsp;&nbsp;
        <?php } ?>
        <img src="<?php echo plugins_url('newsletter'); ?>/images/header/logo.png" style="vertical-align: middle;">

        <a href="http://www.thenewsletterplugin.com/?utm_source=plugin&utm_medium=link&utm_campaign=newsletter-extensions&utm_content=<?php echo NEWSLETTER_VERSION?>" target="_blank" style="font-weight: bold; font-size: 13px; text-transform: uppercase">
            Get the Professional Extensions!
        </a>
        &nbsp;&nbsp;&nbsp;
        <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-documentation" target="_blank">
            <i class="fa fa-file-text"></i> Documentation
        </a>
        &nbsp;&nbsp;
        <a href="http://www.thenewsletterplugin.com/forums" target="_blank">
            <i class="fa fa-life-ring"></i> Forum
        </a>
        &nbsp;&nbsp;
        <a href="https://www.facebook.com/thenewsletterplugin
           " target="_blank">
            <i class="fa fa-facebook-square"></i> Facebook
        </a>
        &nbsp;&nbsp;
        Stay updated: 
        <form target="_blank" style="display: inline" action="http://www.thenewsletterplugin.com/wp-content/plugins/newsletter/subscribe.php" method="post">
            <input type="email" name="ne" placeholder="Your email" required size="30" value="<?php echo esc_attr($current_user->user_email)?>">
            <input type="hidden" name="nr" value="plugin">
            <input type="submit" value="Go">
        </form>

    </div>
<?php } ?>

<?php if (NEWSLETTER_DEBUG || !isset($dismissed['rate']) && $user_count > 200) { ?>
    <div class="notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&dismiss=rate' ?>" class="dismiss">&times;</a>
        <p>
            We never asked before and we're curious: <a href="http://wordpress.org/extend/plugins/newsletter/" target="_blank">would you rate this plugin</a>?
            (few seconds required - account on WordPress.org required, every blog owner should have one...). <strong>Really appreciated, The Newsletter Team</strong>.
        </p>
    </div>
<?php } ?>

<?php if (NEWSLETTER_DEBUG || !isset($dismissed['tracking-url'])) { ?>
    <div class="notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&dismiss=tracking-url' ?>" class="dismiss">&times;</a>
        <p>
            There is a new option avoid to hit spam filters available on statistics panel. Check it out.
        </p>
    </div>
<?php } ?>

<?php if (NEWSLETTER_DEBUG || !isset($dismissed['newsletter-page']) && empty(NewsletterSubscription::instance()->options['url'])) { ?>
    <div class="notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&dismiss=newsletter-page' ?>" class="dismiss">&times;</a>
        <p>
            You should create a blog page to show the subscription form and the subscription messages. Go to the
            <a href="?page=newsletter_subscription_options">subscription panel</a> to
            configure it.
        </p>
    </div>
<?php } ?>


<?php $newsletter->warnings(); ?>
