<?php

include '../../../../wp-load.php';

if (!check_admin_referer())
    die('Only the administrator can view the preview');

// Used by theme code
$theme_options = NewsletterEmails::instance()->get_current_theme_options();
$theme_url = NewsletterEmails::instance()->get_current_theme_url();
header('Content-Type: text/html;charset=UTF-8');

include(NewsletterEmails::instance()->get_current_theme_file_path('theme.php'));
