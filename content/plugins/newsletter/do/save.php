<?php

header('Content-Type: text/html;charset=UTF-8');
header('X-Robots-Tag: noindex,nofollow,noarchive');
header('Cache-Control: no-cache,no-store,private');
include '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->save_profile();
// $user->alert is a temporary field
NewsletterSubscription::instance()->show_message('profile', $user, $user->alert);
