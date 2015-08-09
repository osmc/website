<?php
/*
 * This is a pre packaged theme options page. Every option name
 * must start with "theme_" so Newsletter can distinguish them from other
 * options that are specific to the object using the theme.
 *
 * An array of theme default options should always be present and that default options
 * should be merged with the current complete set of options as shown below.
 *
 * Every theme can define its own set of options, the will be used in the theme.php
 * file while composing the email body. Newsletter knows nothing about theme options
 * (other than saving them) and does not use or relies on any of them.
 *
 * For multilanguage purpose you can actually check the constants "WP_LANG", until
 * a decent system will be implemented.
 */
$theme_defaults = array(
    'theme_max_posts'=>5,
    'theme_read_more'=>'Read More',
    'theme_pre_message'=>'This email has been sent to {email} because subscribed and confirmed on ' . get_option('blogname') . '. <a href="{profile_url}">Click here to modify you subscription or unsubscribe</a>.',
    'theme_categories'=>array()
    );

// Mandatory!
$controls->merge_defaults($theme_defaults);
?>

<table class="form-table">
    <tr valign="top">
        <th>Max new posts to include</th>
        <td>
            <?php $controls->text('theme_max_posts', 5); ?> (it defaults to 10 if empty or invalid)
        </td>
    </tr>
    <tr valign="top">
        <th>Categories to include</th>
        <td><?php $controls->categories_group('theme_categories'); ?></td>
    </tr>
    <tr>
        <th>Post types</th>
        <td>
            <?php $controls->post_types('theme_post_types'); ?>
            <p class="description">Leave all uncheck for a default behavior.</p>
        </td>
    </tr>
    <tr valign="top">
        <th>Pre message</th>
        <td>
            <?php $controls->text('theme_pre_message', 70); ?>
        </td>
    </tr>
    <tr valign="top">
        <th>Read more label</th>
        <td>
            <?php $controls->text('theme_read_more'); ?>
        </td>
    </tr>
</table>

<?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social-options.php'; ?>
