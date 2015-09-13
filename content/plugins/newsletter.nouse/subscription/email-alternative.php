<?php
/*
 * Copy this file into
 *
 *     wp-content/extensions/newsletter/subscription
 *
 * and RENAME it to email.php.
 *
 * It will be used insted of the standard email.php file to generate the body of
 * confirmation and welcome emails.
 *
 * A globally available $message variable contains the generated message as resulted
 * by merging your configured message (on subscription steps panel) and the user's
 * data.
 *
 */
?>
<html>
  <head>
    <style type="text/css">
    </style>
  </head>

  <body style="font-family: sans-serif; font-size: 12px">

      <?php // NEVER FORGET THIS LINE! ?>
      <?php echo $message; ?>

  </body>
</html>