<?php
header('Content-Type: text/html;charset=UTF-8');
header('X-Robots-Tag: noindex,nofollow,noarchive');
header('Cache-Control: no-cache,no-store,private');
// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);
if (!defined('ABSPATH')) {
    include '../../../../wp-load.php';
}

$user = NewsletterSubscription::instance()->check_user();

if ($user == null || $user->status != 'C') {
  echo 'Subscriber not found, sorry.';
  die();
}

$options_main = get_option('newsletter_main', array());

setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');

header('Location: ' . $options_main['lock_url']);

die();
