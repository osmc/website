<?php

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$options = $controls->data;
$options_lists = get_option('newsletter_profile');
$options_profile = get_option('newsletter_profile');
$options_main = get_option('newsletter_main');

// Move to base zero
if ($controls->is_action()) {
    $controls->data['search_page'] = (int)$controls->data['search_page']-1;
    $module->save_options($controls->data, 'search');
}
else {
    $controls->data = $module->get_options('search');
    if (empty($controls->data['search_page'])) $controls->data['search_page'] = 0;
}

$lists = array(''=>'Any');
for ($i=1; $i<=NEWSLETTER_LIST_MAX; $i++)
{
    if (empty($options_lists['list_' . $i])) continue;
    $lists[''.$i] = '(' . $i . ') ' . $options_lists['list_' . $i];
}

if ($controls->is_action('resend')) {
    $user = NewsletterUsers::instance()->get_user($controls->button_data);
    $opts = get_option('newsletter');
    NewsletterSubscription::instance()->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
    $controls->messages = 'Activation email resent to ' . $user->email;
}

if ($controls->is_action('resend_welcome')) {
    $user = NewsletterUsers::instance()->get_user($controls->button_data);
    $opts = get_option('newsletter');
    NewsletterSubscription::instance()->mail($user->email, $newsletter->replace($opts['confirmed_subject'], $user), $newsletter->replace($opts['confirmed_message'], $user));
    $controls->messages = 'Welcome email resent.';
}

if ($controls->is_action('remove')) {
    $wpdb->query($wpdb->prepare("delete from " . NEWSLETTER_USERS_TABLE . " where id=%d", $controls->button_data));
    unset($controls->data['subscriber_id']);
}

// We build the query condition
$where = "where 1=1";
$query_args = array();
$text = trim($controls->data['search_text']);
if ($text != '') {
    $query_args[] = '%' . $text . '%';
    $query_args[] = '%' . $text . '%';
    $query_args[] = '%' . $text . '%';
    $where .= " and (email like %s or name like %s or surname like %s)";
}

//if (isset($controls->data['search_test'])) {
//    $where .= " and test=1";
//}

if (!empty($controls->data['search_status'])) {
    if ($controls->data['search_status'] == 'T') {
        $where .= " and test=1";
    } else {
        $query_args[] = $controls->data['search_status'];
        $where .= " and status=%s";
    }
}

// Total items, total pages
$items_per_page = 20;
$where = $wpdb->prepare($where, $query_args);
$count = Newsletter::instance()->store->get_count(NEWSLETTER_USERS_TABLE, $where);
$last_page = floor($count / $items_per_page) - ($count % $items_per_page == 0 ? 1 : 0);
if ($last_page < 0) $last_page = 0;

if ($controls->is_action('last')) {
    $controls->data['search_page'] = $last_page;
}
if ($controls->is_action('first')) {
    $controls->data['search_page'] = 0;
}
if ($controls->is_action('next')) {
    $controls->data['search_page'] = (int)$controls->data['search_page']+1;
}
if ($controls->is_action('prev')) {
    $controls->data['search_page'] = (int)$controls->data['search_page']-1;
}
if ($controls->is_action('search')) {
    $controls->data['search_page'] = 0;
}

// Eventually fix the page
if ($controls->data['search_page'] < 0) $controls->data['search_page'] = 0;
if ($controls->data['search_page'] > $last_page) $controls->data['search_page'] = $last_page;

$query = "select * from " . NEWSLETTER_USERS_TABLE . ' ' . $where . " order by id desc";
$query .= " limit " . ($controls->data['search_page']*$items_per_page) . "," . $items_per_page;
$list = $wpdb->get_results($query);

// Move to base 1
$controls->data['search_page']++;
?>

<div class="wrap">

    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>



    <div id="newsletter-title">
         <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>
    <h2>Subscriber Search</h2>
    </div>

    <div class="newsletter-separator"></div>

    <?php $controls->show(); ?>

    <form id="channel" method="post" action="">
        <?php $controls->init(); ?>

        <div style="padding: .6em; border: 1px solid #ddd; background-color: #f4f4f4; border-radius: 3px;">
            <?php $controls->text('search_text', 80, __('Search text', 'newsletter-users')); ?>

            <?php _e('filter by', 'newsletter-users')?>:<?php $controls->select('search_status', array(''=>'Any status', 'T'=>'Test subscribers', 'C'=>'Confirmed', 'S'=>'Not confirmed', 'U'=>'Unsubscribed', 'B'=>'Bounced')); ?>
            <?php $controls->button('search', __('Search', 'newsletter-users')); ?>
            <br>
            <?php $controls->checkbox('show_preferences', __('Show preferences', 'newsletter-users')); ?>
        </div>




<div class="newsletter-paginator">

<?php $controls->button('first', '«'); ?>
<?php $controls->button('prev', '‹'); ?>
<?php $controls->text('search_page', 3); ?> of <?php echo $last_page+1 ?> <?php $controls->button('go', __('Go', 'newsletter-users')); ?>
<?php $controls->button('next', '›'); ?>
<?php $controls->button('last', '»'); ?>
</div>

        <?php echo $count ?> <?php _e('subscribers found', 'newsletter-users')?>
<?php if ($where != "where 1=1") { ?>
    <p><strong><?php _e('The list is filtered, see the filters above.', 'newsletter-users') ?></strong></p>
<?php } ?>

<table class="widefat">
    <thead>
<tr>
    <th>Id</th>
    <th>Email/Name</th>
    <?php if (isset($options['show_profile']) && $options['show_profile'] == 1) { ?>
      <th>Profile</th>
    <?php } ?>
    <th><?php _e('Status', 'newsletter-users') ?></th>
    <?php if (isset($options['show_preferences']) && $options['show_preferences'] == 1) { ?>
      <th><?php _e('Preferences', 'newsletter-users') ?></th>
    <?php } ?>
    <th>Actions</th>
    <?php if (isset($options['search_clicks']) && $options['search_clicks'] == 1) { ?>
    <th>Clicks</th>
    <?php } ?>
</tr>
    </thead>
    <?php foreach($list as $s) { ?>
<tr class="<?php echo ($i++%2==0)?'alternate':''; ?>">

<td>
    <?php echo $s->id; ?>
</td>

<td>
    <?php echo $s->email; ?><br /><?php echo $s->name; ?> <?php echo $s->surname; ?>
</td>


<?php if (isset($options['show_profile']) && $options['show_profile'] == 1) { ?>
<td>
    <small>
    <?php
    for ($i=1; $i<NEWSLETTER_PROFILE_MAX; $i++) {
        if ($options_profile['profile_' . $i] == '') continue;
        echo $options_profile['profile_' . $i];
        echo ':';
        $key = 'profile_' . $i;
        echo htmlspecialchars($s->$key);
        echo '<br />';
    }
    ?>
    </small>
</td>
<?php } ?>

<td>
    <small>
        <?php
        switch ($s->status) {
            case 'S': _e('NOT CONFIRMED', 'newsletter-users'); break;
            case 'C': _e('CONFIRMED', 'newsletter-users'); break;
            case 'U': _e('UNSUBSCRIBED', 'newsletter-users'); break;
            case 'B': _e('BOUNCED', 'newsletter-users'); break;
        }
        ?>
    </small>
</td>

<?php if (isset($options['show_preferences']) && $options['show_preferences'] == 1) { ?>
<td>
    <small>
        <?php
        for ($i=1; $i<=NEWSLETTER_LIST_MAX; $i++) {
            $l = 'list_' . $i;
            if ($s->$l == 1) echo $lists['' . $i] . '<br />';
        }
        ?>
    </small>
</td>
<?php } ?>

<td>
    <a class="button-secondary" href="<?php echo $module->get_admin_page_url('edit'); ?>&amp;id=<?php echo $s->id; ?>"><?php _e('Edit', 'newsletter-users') ?></a>
    <?php $controls->button_confirm('remove', __('Remove', 'newsletter-users'), __('Proceed?', 'newsletter-users'), $s->id); ?>

    <?php //$controls->button('status', 'Confirm', 'newsletter_set_status(this.form,' . $s->id . ',\'C\')'); ?>
    <?php //$controls->button('status', 'Unconfirm', 'newsletter_set_status(this.form,' . $s->id . ',\'S\')'); ?>

    <?php $controls->button_confirm('resend', __('Resend confirmation', 'newsletter-users'), __('Proceed?', 'newsletter-users'), $s->id); ?>
    <?php $controls->button_confirm('resend_welcome', __('Resend welcome', 'newsletter-users'), __('Proceed?', 'newsletter-users'), $s->id); ?>
    <a href="<?php echo plugins_url('newsletter/do/profile.php'); ?>?nk=<?php echo $s->id . '-' . $s->token; ?>" class="button" target="_blank"><?php _e('Profile page', 'newsletter-users') ?></a>
</td>


</tr>
<?php } ?>
</table>
<div class="newsletter-paginator">

<?php $controls->button('first', '«'); ?>
<?php $controls->button('prev', '‹'); ?>
<?php $controls->text('search_page', 3); ?> of <?php echo $last_page+1 ?> <?php $controls->button('go', __('Go', 'newsletter-users')); ?>
<?php $controls->button('next', '›'); ?>
<?php $controls->button('last', '»'); ?>
</div>
    </form>
</div>
