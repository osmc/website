<?php
// This page is used to show subscription messages to users along the various
// subscription and unsubscription steps.
//
// This page is used ONLY IF, on main configutation, you have NOT set a specific
// WordPress page to be used to show messages and when there are no alternative
// URLs specified on single messages.
//
// To create an alternative to this file, just copy the page-alternative.php on
//
//   wp-content/extensions/newsletter/subscription/page.php
//
// and modify that copy.

include '../../../../wp-load.php';

$module = NewsletterSubscription::instance();
$user = $module->get_user_from_request();
$message_key = $module->get_message_key_from_request();
$message = $newsletter->replace($module->options[$message_key . '_text'], $user);
$message .= $module->options[$message_key . '_tracking'];
$alert = stripslashes($_REQUEST['alert']);

// Force the UTF-8 charset
header('Content-Type: text/html;charset=UTF-8');

if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/subscription/page.php')) {
    include WP_CONTENT_DIR . '/extensions/newsletter/subscription/page.php';
    die();
}
?>
<html>
    <head>
        <style type="text/css">
            body {
                font-family: verdana;
                background-color: #ddd;
                font-size: 12px;
            }
            #container {
                border: 1px solid #aaa;
                border-radius: 5px;
                background-color: #fff;
                margin: 40px auto;
                width: 600px;
                padding: 20px
            }
            h1 {
                font-size: 24px;
                font-weight: normal;
                border-bottom: 1px solid #aaa;
                margin-top: 0;
            }
            h2 {
                font-size: 20px;
            }
            th, td {
                font-size: 12px;
            }
            th {
                padding-right: 10px;
                text-align: right;
                vertical-align: middle;
                font-weight: normal;
            }
        </style>
    </head>

    <body>
        <?php if (!empty($alert)) { ?>
        <script>
            alert("<?php echo addslashes(strip_tags($alert)); ?>");
        </script>
        <?php } ?>
        <div id="container">
            <h1><?php echo get_option('blogname'); ?></h1>
            <?php echo $message; ?>
        </div>
    </body>
</html>