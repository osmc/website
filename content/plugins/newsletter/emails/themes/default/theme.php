<?php
/*
 * Name: Default
 * Type: standard
 * Some variables are already defined:
 *
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 */

global $newsletter, $post;

$color = $theme_options['theme_color'];
if (empty($color)) $color = '#000000';

if (isset($theme_options['theme_posts'])) {
    $filters = array();
    
    if (empty($theme_options['theme_max_posts'])) $filters['showposts'] = 10;
    else $filters['showposts'] = (int)$theme_options['theme_max_posts'];
    
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
        <!-- Not all email client take care of styles inserted here -->
        <style type="text/css" media="all">
            a {
                text-decoration: none;
                color: <?php echo $color; ?>;
            }
        </style>
    </head>
    <body style="background-color: #ddd; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666; margin: 0 auto; padding: 0;">
        <br>
        <table align="center">
            <tr>
                <td valign="top" style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666;">
                    <div style="text-align: left; background-color: #fff; max-width: 500px;">
                        <div style="text-align: center">
                        <?php //HEADER
//                        if (!empty($theme_options['theme_banner'])) { 
//                            echo $theme_options['theme_banner'];
                        if (!empty($theme_options['theme_header_logo']['url'])) { ?>
                            <img style="max-width: 500px" alt="<?php echo $theme_options['main_header_title'] ?>" src="<?php echo $theme_options['theme_header_logo']['url'] ?>" />
                        <?php } elseif (!empty($theme_options['main_header_logo']['url'])) { ?>
                            <img style="max-width: 500px" alt="<?php echo $theme_options['main_header_title'] ?>" src="<?php echo $theme_options['main_header_logo']['url'] ?>" />
                        <?php } elseif (!empty($theme_options['main_header_title'])) { ?>
                             <div style="padding: 30px 0; color: #000; font-size: 28px; background-color: #EFEFEF; border-bottom: 1px solid #ddd; text-align: center;">
                                <?php echo $theme_options['main_header_title'] ?>
                            </div>
                            <?php if (!empty($theme_options['main_header_sub'])) { ?>
                            <div style="padding: 10px 0; color: #000; font-size: 16px; text-align: center;">
                                <?php echo $theme_options['main_header_sub'] ?>
                            </div>
                        <?php } ?>
                        <?php } else { ?>
                            <div style="padding: 30px 20px; color: #000; font-size: 28px; background-color: #EFEFEF; border-bottom: 1px solid #ddd; text-align: center;">
                                <?php echo get_option('blogname'); ?>
                            </div>
                            <?php if (!empty($theme_options['main_header_sub'])) { ?>
                            <div style="padding: 10px 0; color: #000; font-size: 16px; text-align: center;">
                                <?php echo $theme_options['main_header_sub'] ?>
                            </div>
                        <?php } ?>
                        <?php } ?>
                        </div>
                        
                            
                        <div style="padding: 10px 20px 20px 20px; background-color: #fff; line-height: 18px">

                            <p style="text-align: center; font-size: small;"><a target="_blank"  href="{email_url}">View this email online</a></p>

                            <p>Here you can start to write your message. Be polite with your readers! Don't forget the subject of this message.</p>
                            <?php if (!empty($posts)) { ?>
                            <table cellpadding="5">
                                <?php foreach ($posts as $post) { setup_postdata($post); ?>
                                    <tr>
                                        <?php if (isset($theme_options['theme_thumbnails'])) { ?>
                                        <td valign="top"><a target="_blank"  href="<?php echo get_permalink($post); ?>"><img width="75" src="<?php echo newsletter_get_post_image($post->ID); ?>" alt="image"></a></td>
                                        <?php } ?>
                                        <td valign="top">
                                            <a target="_blank"  href="<?php echo get_permalink(); ?>" style="font-size: 20px; line-height: 26px"><?php the_title(); ?></a>
                                            <?php if (isset($theme_options['theme_excerpts'])) newsletter_the_excerpt($post); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php } ?>
                            
                            <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/footer.php'; ?>

                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>