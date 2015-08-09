<?php

header('Content-Type: text/html;charset=UTF-8');
header('X-Robots-Tag: noindex,nofollow,noarchive');
header('Cache-Control: no-cache,no-store,private');
include '../../../../wp-load.php';

$user = Newsletter::instance()->get_user_from_request(true);
$field = $_REQUEST['nf'];
$value = $_REQUEST['nv'];
$url = $_REQUEST['nu'];


switch ($field) {
    case 'sex':
        if (!in_array($value, array('f', 'm', 'n')))
            die('Invalid sex value');
        NewsletterUsers::instance()->set_user_field($user->id, 'sex', $value);
        break;
    // Should be managed by Feed by Mail
    case 'feed':
        if (isset($value) && ($value === '0' || $value === '1')) {
            NewsletterUsers::instance()->set_user_field($user->id, 'feed', $value);
        } else
            die('Invalid feed value');
        break;
}

if (strpos($field, 'preference_') === 0) {
    $idx = (int) substr($field, 11);
    $options_profile = get_option('newsletter_profile');

    if ($options_profile['list_' . $idx . '_status'] == 0) {
        die('Not allowed field.');
    }

    if (isset($value) && ($value === '0' || $value === '1')) {
        NewsletterUsers::instance()->set_user_field($user->id, 'list_' . $idx, $value);
    } else {
        die('Invalid preference value');
    }
}

if (isset($url)) {
    header("Location: $url");
} else {
    NewsletterSubscription::instance()->show_message('profile', $user, NewsletterSubscription::instance()->options['profile_saved']);
}
