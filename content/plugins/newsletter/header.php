<?php
$dismissed = get_option('newsletter_dismissed', array());

if (isset($_REQUEST['dismiss']) && check_admin_referer()) {
    $dismissed[$_REQUEST['dismiss']] = 1;
    update_option('newsletter_dismissed', $dismissed);
}

?>
<?php if (NEWSLETTER_HEADER) { ?>
<div id="newsletter-header">
    <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-documentation" target="_blank">Documentation</a>
    <a href="http://www.thenewsletterplugin.com/forums" target="_blank">Forum</a>

    <!--<a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-collaboration" target="_blank">Collaboration</a>-->

    <form style="display: inline; margin: 0;" action="http://www.thenewsletterplugin.com/wp-content/plugins/newsletter/do/subscribe.php" method="post" target="_blank">
        Subscribe<!-- to thenewsletterplugin.com--> <input type="email" name="ne" required placeholder="Your email">
        <input type="submit" value="Go">
    </form>

    <a href="https://www.facebook.com/thenewsletterplugin
" target="_blank"><img style="vertical-align: bottom" src="<?php echo plugins_url('newsletter'); ?>/images/facebook.png"></a>

    <!--
    <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-delivery-engine" target="_blank">Engine next run in <?php echo wp_next_scheduled('newsletter') - time(); ?> s</a>
    -->
</div>
<?php } ?>

<?php if (isset($dismissed['rate']) && $dismissed['rate'] != 1) { ?>
<div class="newsletter-notice">
    I never asked before and I'm curious: <a href="http://wordpress.org/extend/plugins/newsletter/" target="_blank">would you rate this plugin</a>?
    (few seconds required). (account on WordPress.org required, every blog owner should have one...). <strong>Really appreciated, The Newsletter Team</strong>.
    <div class="newsletter-dismiss"><a href="<?php echo wp_nonce_url($_SERVER['REQUEST_URI'] . '&dismiss=rate')?>">Dismiss</a></div>
    <div style="clear: both"></div>
</div>
<?php } ?>

<?php if (isset($dismissed['newsletter-page']) && $dismissed['newsletter-page'] != 1 && empty(NewsletterSubscription::instance()->options['url'])) { ?>
<div class="newsletter-notice">
    Create a page with your blog style to show the subscription form and the subscription messages. Go to the
    <a href="?page=newsletter_subscription_options">subscription panel</a> to
    configure it.
    <div class="newsletter-dismiss"><a href="<?php echo wp_nonce_url($_SERVER['REQUEST_URI'] . '&dismiss=newsletter-page')?>">Dismiss</a></div>
    <div style="clear: both"></div>
</div>
<?php } ?>


<?php $newsletter->warnings(); ?>
