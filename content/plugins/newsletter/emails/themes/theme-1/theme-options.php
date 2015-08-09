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
    'theme_max_posts'=>10,
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
        <th>Categories</th>
        <td>
            <?php $controls->categories_group('theme_categories'); ?>
        </td>
    </tr>
</table>

<?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social-options.php'; ?>

