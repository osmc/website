<?php

// This page is used to show subscription message on a popup.
//
// The message to be shown is contained on externally prepared variable $message.
//
// To create an alternative to this file, just copy it inside
// 
//   wp-content/newsletter/popup.php
//   
// and modify that copy.
//
// REMEBER TO REMOVE THE LINES OF CODE BELOW IN YOUR COPY!!!

// To be removed on your customzed copy
if (strpos(__FILE__, 'wp-content/newsletter') === false && is_file(WP_CONTENT_DIR . '/newsletter/popup.php')) {
  include WP_CONTENT_DIR . '/newsletter/popup.php';
  die();
}
// End of the code to be removed

?>
<html>
  <head>
    <style type="text/css">
      body {
        font-family: verdana;
        font-size: 12px;
        background-color: #fff;
        margin: 0;
        padding: 0;
      }
      #container {
        background-image: url("<?php echo plugins_url('newsletter'); ?>/images/popup/bg.png");
        background-repeat: repeat-x;
        padding: 10px 15px;
      }
      #title {
        font-size: 24px;
        color: #fff;
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
      
      .newsletter-td-privacy {
        text-align: center;
      }
      
      .newsletter-td-submit {
        text-align: right;
      }
      
      input[type=submit] {
        background-image: url("<?php echo plugins_url('newsletter'); ?>/images/popup/button.png");
        color: #fff;
        border: 1px solid #333;
        padding: 5px;
      }
    </style>  
  </head>

  <body>
    <div id="container">
      <div id="title"><?php echo get_option('blogname'); ?></div>
      <div id="message">
        <?php echo $message; ?>
      </div>
    </div>
  </body>
</html>