<?php
include '../../../../wp-load.php';

list($email_id, $user_id, $url, $anchor, $key) = explode(';', base64_decode($_GET['r']), 5);

$wpdb->insert(NEWSLETTER_STATS_TABLE, array(
    'email_id' => $email_id,
    'user_id' => $user_id,
    'url' => $url,
    'anchor' => $anchor,
    'ip' => $_SERVER['REMOTE_ADDR']
        )
);

$parts = parse_url($url);
$verified = $parts['host'] == $_SERVER['HTTP_HOST'];
if (!$verified) {
    $options = NewsletterStatistics::instance()->options;
    $verified = $key == md5($email_id . ';' . $user_id . ';' . $url . ';' . $anchor . $options['key']);
}

if ($verified) {
    header('Location: ' . $url);
    die();
}
?><html>
    <head>
        <meta http-equiv="refresh" content="2; url=<?php echo htmlspecialchars($url); ?>">
        <style>
            body {
                font-family: sans-serif;
            }
        </style>
    </head>
    <body>
        <div style="max-width: 100%; width: 500px; margin: 40px auto; text-align: center">
            <p>Redirecting to...</p>
            <h3><?php echo htmlspecialchars($url); ?></h3>
        </div>
    </body>
</html>