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

$user = NewsletterSubscription::instance()->get_user_from_request();
if ($user == null) {
    NewsletterSubscription::instance()->show_message('unsubscription_error', null);
}
else {
    NewsletterSubscription::instance()->show_message('unsubscription', $user);
}
