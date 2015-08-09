<?php
/**
 * This is a generic viewer for sent emails. It is not binded to one shot emails, it can display even the emails from
 * updates or feed by mail module.
 */
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

// TODO: Change to Newsletter::instance()->get:email(), not urgent
$email = Newsletter::instance()->get_email((int)$_GET['id']);
if (empty($email)) die('Email not found');

$user = NewsletterSubscription::instance()->get_user_from_request();

if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/view.php')) {
  include WP_CONTENT_DIR . '/extensions/newsletter/view.php';
  die();
}

echo $newsletter->replace($email->message, $user, $email->id);
die();
?>