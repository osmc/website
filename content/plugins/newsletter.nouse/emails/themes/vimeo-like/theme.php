<?php
global $newsletter; // Newsletter object
global $post; // Current post managed by WordPress

/*
 * Some variabled are prepared by Newsletter Plus and are available inside the theme,
 * for example the theme options used to build the email body as configured by blog
 * owner.
 *
 * $theme_options - is an associative array with theme options: every option starts
 * with "theme_" as required. See the theme-options.php file for details.
 * Inside that array there are the autmated email options as well, if needed.
 * A special value can be present in theme_options and is the "last_run" which indicates
 * when th automated email has been composed last time. Is should be used to find if
 * there are now posts or not.
 *
 * $is_test - if true it means we are composing an email for test purpose.
 */


// This array will be passed to WordPress to extract the posts
$filters = array();

// Maximum number of post to retrieve
$filters['showposts'] = (int) $theme_options['theme_max_posts'];
if ($filters['showposts'] == 0) {
    $filters['showposts'] = 10;
}


// Include only posts from specified categories. Do not filter per category is no
// one category has been selected.
if (is_array($theme_options['theme_categories'])) {
    $filters['cat'] = implode(',', $theme_options['theme_categories']);
}

if (!empty($theme_options['theme_post_types'])) {
    $filters['post_type'] = $theme_options['theme_post_types'];
}

// Retrieve the posts asking them to WordPress
$posts = get_posts($filters);

// Styles
$color = $theme_options['theme_color'];
if (empty($color))
    $color = '#777';

$font = $theme_options['theme_font'];
$font_size = $theme_options['theme_font_size'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <style>
            * {
                font-family: <?php echo $font; ?>;
                font-size: <?php echo $font_size; ?>;
            }
        </style>
    </head>
    <body style="font:normal 11px helvetica,sans-serif;">

        <table style="background:#ffffff" width="600" align="center" border="0" cellpadding="0" cellspacing="0">

            <tr>
                <td style="color:#9ab;font:normal 11px helvetica,sans-serif;text-align:center;padding:10px 0 20px 0"><?php echo $theme_options['theme_pre_message']; ?></td>
            </tr>

            <tr>
                <td><img src="<?php echo $theme_url; ?>/bg_header_email.gif" alt=""></td>
            </tr>

            <tr>

                <td style="border:1px dotted #e1e2e3;border-top:none;border-bottom:3px solid #e1e2e3;background:#ffffff">



                    <table width="100%" align="center" border="0" cellpadding="20" cellspacing="0">

                        <tr>
                            <td style="background:#ffffff">



                                <p style="color:#456;font-family:arial,sans-serif;font-size:24px;line-height:1.2;margin:15px 0;padding:0"><a target="_tab" href="<?php echo get_option('home'); ?>" style="color:#28c;text-decoration:none" target="_blank"><?php echo get_option('blogname'); ?></a></p>


                                <?php
                                foreach ($posts as $post) {
                                    setup_postdata($post);
                                    $image = nt_post_image(get_the_ID(), 'thumbnail');
                                    ?>


                                    <table style="width:100%;color:#456;font:normal 12px/1.5em helvetica,sans-serif;margin:15px 0 0 0;padding:0 0 15px 0;border-bottom:1px dotted #e1e2e3">

                                        <tbody><tr>

                                                <td style="width:100%;padding:0 10px 0 0;vertical-align:top">

                                                    <p style="font-family:arial,sans-serif;color:#456;font-size:20px;line-height:22px;margin:0;padding:0"><strong><a target="_tab" href="<?php echo get_permalink(); ?>" style="color:#456;text-decoration:none" target="_blank"><?php the_title(); ?></a></strong></p>

                                                    <p style="font-family:arial,sans-serif;line-height:1.5em;margin:15px 0;padding:0"><?php the_excerpt(); ?>. </p>

                                                </td>

                                                <td style="vertical-align:middle; width: 100px">

                                                    <a target="_tab" href="<?php echo get_permalink(); ?>" target="_blank"><img src="<?php echo $image; ?>" alt="" width="100" border="0" height="100"></a>

                                                    <p style="background:#2786c2;text-align:center;margin:10px 0 0 0;font-size:11px;line-height:14px;font-family:arial,sans-serif;padding:4px 2px;border-radius:4px"><a target="_tab" href="<?php echo get_permalink(); ?>" style="color:#fff;text-decoration:none" target="_blank"><strong><?php echo $theme_options['theme_read_more']; ?></strong></a></p>

                                                </td>

                                            </tr>

                                        </tbody></table>

                                    <br>
                                    <?php
                                }
                                ?>




                                <br><br>
                                <p style="color:#456;font-family:arial,sans-serif;font-size:12px;line-height:1.6em;font-style:italic;margin:0 0 15px 0;padding:0">
                                    To change your subscription, <a target="_blank"  href="{profile_url}">click here</a>.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>

            <?php if (!isset($theme_options['theme_social_disable'])) { ?>
            <tr>
                <td style="font:normal 11px helvetica,sans-serif;">
                     <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social.php'; ?>
                </td>
            </tr>
            <?php } ?>
         </table>

    </body>
</html>

