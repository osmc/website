<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>

        <style type="text/css">
            .ExternalClass {width:100%;}

            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
                line-height: 100%;
            }

            body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}

            body {margin:0; padding:0;}

            table td {border-collapse:collapse;}

            p {margin:0; padding:0; margin-bottom:1em;}

            h1, h2, h3, h4, h5, h6 {
                color: #666;
                line-height: 100%;
            }

            a, a:link {
                color:#2A5DB0;
                text-decoration: underline;
            }

            body, #body_style {
                background:#A52B00;
                min-height:1000px;
                xcolor:#000;
                font-family:Arial, Helvetica, sans-serif;
                font-size:14px;
            }

            span.yshortcuts { color:#000; background-color:none; border:none;}
            span.yshortcuts:hover,
            span.yshortcuts:active,
            span.yshortcuts:focus {color:#000; background-color:none; border:none;}

            a:visited { color: #3c96e2; text-decoration: none}
            a:focus   { color: #3c96e2; text-decoration: underline}
            a:hover   { color: #3c96e2; text-decoration: underline}

            @media only screen and (max-device-width: 480px) {


                body[yahoo] #container1 {display:block !important}
                body[yahoo] p {font-size: 10px}

            }

            @media only screen and (min-device-width: 768px) and (max-device-width: 1024px)  {


                body[yahoo] #container1 {display:block !important}
                body[yahoo] p {font-size: 14px}

            }

        </style>


    </head>
    <body style="margin-top: 0; background:#A52B00; min-height:1000px; color:#000;font-family:Arial, Helvetica, sans-serif; font-size:14px"
          alink="#FF0000" link="#FF0000" bgcolor="#A52B00" text="#000000" yahoo="fix">

        <div id="body_style" style="padding:0px">

            <table cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" width="600" align="center">
                <tr>
                    <td width="600" colspan="3"><img src="<?php echo $theme_url; ?>/images/header.png"></td>
                </tr>
                <tr>
                    <td width="600" align="center" colspan="3">
                        <h1><?php echo get_option('blogname'); ?></h1>
                    </td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="560">
                        <p>Hi {name},</p>

                        <p>The Newsletter Team wishes you merry Christmas and happy New Year. This time of year is special for us and we would like
                            to share this time with you, also.</p>

                        <p>We'll take a rest for few days with our families but we have a <strong>big surprise</strong> for YOU that will
                            be unvealed the first days of the New Year.</p>

                        <p>Curious? You can <a href="<?php echo get_option('blogname'); ?>">discover a little more right now</a>.</p>

                        <p>
                            See you soon, TNT.<br>
                                <a href="<?php echo get_option('blogname'); ?>">http://www.thenewsletterplugin.com</a>
                        </p>

                    </td>
                    <td width="20">&nbsp;</td>
                </tr>
                <td width="600" colspan="3">
                    <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social.php'; ?>
                </td>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="560" style="color:#666">
                        <p>To change your subscription, <a target="_blank"  href="{profile_url}">click here</a>.
                    </td>
                    <td width="20">&nbsp;</td>
                </tr>
                <tr>
                    <td width="600" colspan="3" bgcolor="#A52B00"><img src="<?php echo $theme_url; ?>/images/footer.png"></td>
                </tr>
            </table>

        </div>

    </body>
</html>