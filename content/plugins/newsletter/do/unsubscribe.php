<?php
header('Content-Type: text/html;charset=UTF-8');
header('X-Robots-Tag: noindex,nofollow,noarchive');
header('Cache-Control: no-cache,no-store,private');

// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);
if (!defined('ABSPATH')) {
    require_once '../../../../wp-load.php';
}

if (isset($_GET['ts']) && time() - $_GET['ts'] < 30) {

    $user = NewsletterSubscription::instance()->unsubscribe();
    if ($user->status == 'E') {
        NewsletterSubscription::instance()->show_message('unsubscription_error', $user);
    } else {
        NewsletterSubscription::instance()->show_message('unsubscribed', $user);
    }
} else {
    $url = plugins_url('newsletter') . '/do/unsubscribe.php?';
    foreach ($_REQUEST as $name => $value) {
        $url .= urlencode($name) . '=' . urlencode($value) . '&';
    }
    $url .= '&ts=' . time();
    ?><!DOCTYPE html>
    <html>
        <head>
            <script>
                location.href = location.href + "&ts=<?php echo time(); ?>";
            </script>
        </head>
        <body>
            If you're not redirect in few seconds, <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&ts=<?php echo time(); ?>">click here</a>, thank you.
        </body>
    </html>
    <?php
}
?>
