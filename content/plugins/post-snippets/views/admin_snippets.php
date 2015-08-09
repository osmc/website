<form method="post" action="">
    <?php wp_nonce_field('update_snippets', 'update_snippets_nonce'); ?>

    <table class="widefat fixed" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" class="check-column"><input type="checkbox" /></th>
            <th scope="col" style="width: 180px;"><?php _e('Title', PostSnippets::TEXT_DOMAIN); ?></th>
            <th scope="col" style="width: 180px;"><?php _e('Variables', PostSnippets::TEXT_DOMAIN); ?></th>
            <th scope="col"><?php _e('Snippet', PostSnippets::TEXT_DOMAIN); ?></th>
        </tr>
        </thead>
    
        <tfoot>
        <tr>
            <th scope="col" class="check-column"><input type="checkbox" /></th>
            <th scope="col"><?php _e('Title', PostSnippets::TEXT_DOMAIN) ?></th>
            <th scope="col"><?php _e('Variables', PostSnippets::TEXT_DOMAIN) ?></th>
            <th scope="col"><?php _e('Snippet', PostSnippets::TEXT_DOMAIN) ?></th>
        </tr>
        </tfoot>
    
        <tbody>
<?php 
$snippets = get_option(PostSnippets::OPTION_KEY);
if (!empty($snippets)) {
    foreach ($snippets as $key => $snippet) {
        ?>
            <tr class='recent'>
            <th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='<?php echo $key;
        ?>' /></th>
            <td class='row-title'>
            <input type='text' name='<?php echo $key;
        ?>_title' value='<?php echo $snippet['title'];
        ?>' />
            </td>
            <td class='name'>
            <input type='text' name='<?php echo $key;
        ?>_vars' value='<?php echo $snippet['vars'];
        ?>' />
            <br/>
            <br/>
            <?php
            PostSnippets_Admin::checkbox(__('Shortcode', PostSnippets::TEXT_DOMAIN), $key.'_shortcode',
                            $snippet['shortcode']);

        echo '<br/><strong>Shortcode Options:</strong><br/>';

        if (!defined('POST_SNIPPETS_DISABLE_PHP')) {
            PostSnippets_Admin::checkbox(
                __('PHP Code', PostSnippets::TEXT_DOMAIN),
                $key.'_php',
                $snippet['php']
            );
        }

        $wptexturize = isset($snippet['wptexturize']) ? $snippet['wptexturize'] : false;
        PostSnippets_Admin::checkbox('wptexturize', $key.'_wptexturize', $wptexturize);
        ?>
            </td>
            <td class='desc'>
            <textarea name="<?php echo $key;
        ?>_snippet" class="large-text" style='width: 100%;' rows="5"><?php echo htmlspecialchars($snippet['snippet'], ENT_NOQUOTES);
        ?></textarea>
            <?php _e('Description', PostSnippets::TEXT_DOMAIN) ?>:
            <input type='text' style='width: 100%;' name='<?php echo $key;
        ?>_description' value='<?php if (isset($snippet['description'])) {
    echo esc_html($snippet['description']);
}
        ?>' /><br/>
            </td>
            </tr>
        <?php

    }
}
        ?>
        </tbody>
    </table>

<?php
        PostSnippets_Admin::submit('update-snippets', __('Update Snippets', PostSnippets::TEXT_DOMAIN));
        PostSnippets_Admin::submit('add-snippet', __('Add New Snippet', PostSnippets::TEXT_DOMAIN), 'button-secondary', false);
        PostSnippets_Admin::submit('delete-snippets', __('Delete Selected', PostSnippets::TEXT_DOMAIN), 'button-secondary', false);
        echo '</form>';
