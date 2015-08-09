<?php

require_once '../../../../wp-load.php';

if (!isset($newsletter)) $newsletter = new Newsletter();

$options_main = get_option('newsletter_main', array());
$options = get_option('newsletter', array());

$text = trim($options['subscription_popup_text']);
if (empty($text)) $text = $options['subscription_text'];

if (stripos($text, '<form') !== false) {
  $message = str_ireplace('<form', '<form method="post" action="' . NEWSLETTER_SUBSCRIBE_POPUP_URL . '" onsubmit="return newsletter_check(this)"', $options['subscription_text']);
  $message = $this->replace_lists($message);
} else {
  $form = $newsletter->subscription_form(null, true, NEWSLETTER_SUBSCRIBE_POPUP_URL);

  if (strpos($options['subscription_text'], '{subscription_form}') !== false)
      $message = str_replace('{subscription_form}', $form, $text);
  else $message = $text . $form;
}
include NEWSLETTER_DIR . '/popup.php';
