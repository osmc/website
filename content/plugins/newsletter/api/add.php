<?php

include '../../../../wp-load.php';

if (!isset($newsletter)) $newsletter = new Newsletter();

$key = stripslashes($_REQUEST['nk']);
if (empty($newsletter->options['api_key']) || $key != $newsletter->options['api_key']) {
    die('Wrong API key');
}

if (!is_email($_REQUEST['ne'])) {
    die('Wrong email');
}

$subscriber = array();
$subscriber['name'] = stripslashes($_REQUEST['nn']);
$subscriber['surname'] = stripslashes($_REQUEST['ns']);
$subscriber['email'] = $newsletter->normalize_email(stripslashes($_REQUEST['ne']));

if (is_array($_REQUEST['nl'])) {
  foreach ($_REQUEST['nl'] as $add_list) {
    $subscriber['list_' . $add_list] = 1;
  }
}
else if (!empty($_REQUEST['nl'])) {
  $add_lists = explode('|', $_REQUEST['nl']);
  foreach ($add_lists as $add_list) {
    $subscriber['list_' . $add_list] = 1;
  }
}

$options_feed = get_option('newsletter_feed', array());
if ($options_feed['add_new'] == 1) $subscriber['feed'] = 1;

$options_followup = get_option('newsletter_followup', array());
if ($options_followup['add_new'] == 1) {
  $subscriber['followup'] = 1;
  $subscriber['followup_time'] = time() + $options_followup['interval'] * 3600;
}

$subscriber['status'] = 'C';

// TODO: add control for already subscribed emails
NewsletterUsers::instance()->save_user($subscriber);
die('ok');
