<?php

global $newsletter;

require_once '../../../../wp-load.php';
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

// TODO: Check the user capabilities
if (current_user_can('manage_options') || ($newsletter->options['editor'] == 1 && current_user_can('manage_categories'))) {
    $controls = new NewsletterControls();

    if ($controls->is_action('export')) {
        NewsletterUsers::instance()->export();
    }
} else {
    die('Not allowed.');
}


