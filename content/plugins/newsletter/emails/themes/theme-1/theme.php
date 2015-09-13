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
if ($filters['showposts'] == 0)
    $filters['showposts'] = 10;


// Include only posts from specified categories. Do not filter per category is no
// one category has been selected.
if (is_array($theme_options['theme_categories'])) {
    $filters['cat'] = implode(',', $theme_options['theme_categories']);
}

// Retrieve the posts asking them to WordPress
$posts = get_posts($filters);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title></title>
    </head>

    <body>
        <table bgcolor="#c0c0c0" width="100%" cellpadding="20" cellspacing="0" border="0">
            <tr>
                <td align="center">
                    <table width="500" bgcolor="#ffffff" align="center" cellspacing="10" cellpadding="0" style="border: 1px solid #666;">
                        <tr>
                            <td style="font-size: 30px">
                                <i><?php echo get_option('blogname'); ?></i>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px solid #eee; border-bottom: 1px solid #eee; font-size: 12px; color: #999">
                                <br />NEWSLETTER<br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; color: #666">
                                <p>Dear {name}, here an update from <?php echo get_option('blogname'); ?>.</p>
                            </td>
                        </tr>
                        <?php
                        // Do not use &post, it leads to problems...
                        foreach ($posts as $post) {

                            // Setup the post (WordPress requirement)
                            setup_postdata($post);

                            // The theme can "suggest" a subject replacing the one configured, for example. In this case
                            // the theme, is there is no subject, suggest the first post title.
                            if (empty($theme_options['subject']))
                                $theme_options['subject'] = $post->post_title;

                            // Extract a thumbnail, return null if no thumb can be found
                            $image = nt_post_image(get_the_ID());
                            ?>
                            <tr>
                                <td style="font-size: 14px; color: #666">
                                    <?php if ($image != null) { ?>
                                        <img src="<?php echo $image; ?>" alt="picture" align="left"/>
                                    <?php } ?>
                                    <p><a target="_tab" href="<?php echo get_permalink(); ?>" style="font-size: 16px; color: #000; text-decoration: none"><?php the_title(); ?></a></p>

                                    <?php the_excerpt(); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php if (!isset($theme_options['theme_social_disable'])) { ?> 
                            <tr>
                                <td  style="font-family: Arial; font-size: 12px">
                                    <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social.php'; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="border-top: 1px solid #eee; border-bottom: 1px solid #eee; font-size: 12px; color: #999">
                                You received this email because you subscribed for it as {email}. If you'd like, you can <a target="_tab" href="{unsubscription_url}">unsubscribe</a>.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>