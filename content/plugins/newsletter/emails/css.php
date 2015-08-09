<?php

include '../../../../wp-load.php';

$email_id = (int)$_GET['id'];

$body = Newsletter::instance()->get_email_field($email_id, 'message');

$x = strpos($body, '<style');
if ($x === false) return;

$x = strpos($body, '>', $x);
$y = strpos($body, '</style>');

header('Content-Type: text/css');

echo substr($body, $x+1, $y-$x-1);
