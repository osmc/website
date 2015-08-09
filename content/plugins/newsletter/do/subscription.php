<?php

require_once '../../../../wp-load.php';

if (!isset($newsletter)) $newsletter = new Newsletter();

$options_main = get_option('newsletter_main', array());

if (!empty($options_main['url'])) {
  header('Location: ' . $options_main['url']);
  die();
}

$options = get_option('newsletter', array());

if (stripos($options['subscription_text'], '<form') !== false) {
  $message = str_ireplace('<form', '<form method="post" action="' . plugins_url('newsletter/do/subscribe.php') . '" onsubmit="return newsletter_check(this)"', $options['subscription_text']);
  $message = $this->replace_lists($message);
} else {
  $form = $newsletter->subscription_form();

  if (strpos($options['subscription_text'], '{subscription_form}') !== false)
      $message = str_replace('{subscription_form}', $form, $options['subscription_text']);
  else $message = $options['subscription_text'] . $form;
}
include NEWSLETTER_DIR . '/page.php';
