<table class="form-table">
    <tr>
        <th>Max posts</th>
        <td><?php $controls->text('theme_max_posts', 5); ?></td>
    </tr>
    <tr>
        <th>Categories</th>
        <td><?php $controls->categories_group('theme_categories'); ?></td>
    </tr>
    <tr>
        <th>Post types</th>
        <td>
            <?php $controls->post_types('theme_post_types'); ?>
            <p class="description">Leave all uncheck for a default behavior.</p>
        </td>
    </tr>
</table>
<?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social-options.php'; ?>