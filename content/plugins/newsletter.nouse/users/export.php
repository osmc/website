<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$options_profile = get_option('newsletter_profile');
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$lists = array('0' => 'All');
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    if (!isset($options_profile['list_' . $i]))
        $options_profile['list_' . $i] = '';
    $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}
?>

<div class="wrap">
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <div id="newsletter-title">
        <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

        <h2>Subscriber Export</h2>
        <p>
            The import and export functions <strong>ARE NOT for backup</strong>. 
            If you want to backup you should consider to backup the
            wp_newsletter* tables.
        </p>
    </div>
    <div class="newsletter-separator"></div>    


    <form method="post" action="<?php echo plugins_url('newsletter'); ?>/users/csv.php">
        <?php $controls->init(); ?>
        <table class="form-table">
             <tr>
                 <th>Fields separator</th>
                 <td>
                    <?php $controls->select('separator', array(';' => 'Semicolon', ',' => 'Comma', 'tab' => 'Tabulation')); ?>
                     <p class="description">Try to change the separator if Excel does not recognize the columns.</p>
                 </td>
             </tr>
            <tr>
                <th>Limit to</th>
                <td>
                    <?php $controls->select('list', $lists); ?>
                    <p class="description">Limit to the user with that preference active</p>
                </td>
            </tr>
        </table>
        <p>
            <?php $controls->button('export', 'Export'); ?>
        </p>
    </form>

</div>
