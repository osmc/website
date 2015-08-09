<?php

include '../../../../wp-load.php';

if (!isset($newsletter)) $newsletter = new Newsletter();

$key = stripslashes($_REQUEST['nk']);
if (empty(trim($newsletter->options['api_key'])) || $key != $newsletter->options['api_key'])
    die('Wrong API key');

$email = $newsletter->normalize_email(stripslashes($_REQUEST['ne']));
$r = $wpdb->query($wpdb->prepare("delete from " . NEWSLETTER_USERS_TABLE . " where email=%s", $email));
die($r = 0 ? 'ko' : 'ok');