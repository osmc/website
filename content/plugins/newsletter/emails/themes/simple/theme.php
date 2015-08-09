<?php
/*
 * Some variables are already defined:
 *
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 */

global $newsletter, $post;

$color = $theme_options['theme_color'];
if (empty($color))
    $color = '#0088cc';

if (isset($theme_options['theme_posts'])) {
    $filters = array();

    if (empty($theme_options['theme_max_posts']))
        $filters['showposts'] = 10;
    else
        $filters['showposts'] = (int) $theme_options['theme_max_posts'];

    if (!empty($theme_options['theme_categories'])) {
        $filters['category__in'] = $theme_options['theme_categories'];
    }

    if (!empty($theme_options['theme_tags'])) {
        $filters['tag'] = $theme_options['theme_tags'];
    }

    if (!empty($theme_options['theme_post_types'])) {
        $filters['post_type'] = $theme_options['theme_post_types'];
    }

    $posts = get_posts($filters);
}
?><!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
            .ReadMsgBody {
                width: 100%;
            }
            .ExternalClass {
                width: 100%; background-color:#e7e8e9 !important;
            }
            .yshortcuts {color: #2979be;}
            body {
                background-color: #e7e8e9;}

        </style>
    </head>
    <body style="background-color:#e7e8e9;">
        <br>
        <br>
        <table border="0" cellspacing="0" cellpadding="1" width="550" align="center">
            <tbody>
                <tr>
                    <td style="background-color: #fff;" width="550" valign="top">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tbody>
                                <tr>
                                    <td valign="top" style="background-color: #333; color: #f4f4f4; font-size: 20px; padding: 7px">
                                        <?php echo get_option('blogname') ?>
                                    </td>
                                </tr>
                                <!-- main content here --> 
                                <tr>
                                    <td>
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td rowspan="10" width="35"></td>
                                                    <td height="30"><br /></td>
                                                    <td rowspan="9" width="35"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 32px; font-family: Arial; color: #217abe;">Here the title</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 22px; font-family: Arial; color: #262729;">Here the subtitle</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 14px; font-family: Arial; color: #444; text-align: left">
                                                        <p>Hi {name},</p>
                                                        <p>here a great new from me. I absoutely need to share this secret with you.</p>
                                                        <p>Here a unordered list:</p>
                                                        <ul>
                                                            <li>List item 1</li>
                                                            <li>List item 2</li>
                                                            <li>List item 3</li>
                                                        </ul>
                                                        <p>Some other words before say good bye!</p>
                                                        <p>See you soon.</p>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td height="30"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <!-- end main content --> 
                                <tr>
                                    <td>
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td width="35" height="20"></td>
                                                    <td></td>
                                                    <td rowspan="3" width="35"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" style="font-size: 14px; font-family: Arial;">
                                                                        <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social.php'; ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="20"></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #000000;" height="2"></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #1b1c1e; font-size: 13px; color: #f4f4f4;" height="20" align="center">
                                        To unsubscribe <a style="color: #ccc" href="{unsubscription_url}">click here</a>, to edit your profile
                                        <a style="color: #ccc" href="{profile_url}">click here</a>.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>