<?php

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$options = stripslashes_deep($_POST['options']);
$options_lists = get_option('newsletter_profile');
$options_profile = get_option('newsletter_profile');
$options_main = get_option('newsletter_main');

$lists = array(''=>'Any');
for ($i=1; $i<=NEWSLETTER_LIST_MAX; $i++)
{
    if (empty($options_lists['list_' . $i])) continue;
    $lists[''.$i] = '(' . $i . ') ' . $options_lists['list_' . $i];
}

if ($controls->is_action('resend')) {
    $user = NewsletterUsers::instance()->get_user($controls->button_data);
    $opts = get_option('newsletter');
    $newsletter->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
    $controls->messages = 'Activation email resent to ' . $user->email;
}

if ($controls->is_action('resend_welcome')) {
    $user = NewsletterUsers::instance()->get_user($controls->button_data);
    $opts = get_option('newsletter');
    $newsletter->mail($user->email, $newsletter->replace($opts['confirmed_subject'], $user), $newsletter->replace($opts['confirmed_message'], $user));
    $controls->messages = 'Welcome email resent.';
}

if ($controls->is_action('remove')) {
    $wpdb->query($wpdb->prepare("delete from " . $wpdb->prefix . "newsletter where id=%d", $controls->button_data));
    unset($controls->data['subscriber_id']);
}

if ($action == 'status') {
    newsletter_set_status($controls->data['subscriber_id'], $controls->data['subscriber_status']);
}


if ($controls->is_action('search')) {

  if (empty($controls->data['search_order'])) $order = 'email';
  if ($controls->data['search_order'] == 'id') $order = 'id desc';

  $query = "select * from " . NEWSLETTER_USERS_TABLE . " where 1=1";

  if (!empty($controls->data['search_status'])) {
    $query .= " and status='" . $wpdb->escape($controls->data['search_status']) . "'";
  }

  if (!empty($controls->data['search_test'])) {
    $query .= " and test=1";
  }

  if (trim($controls->data['search_text']) != '') {
    $query .= " and (email like '%" .
            $wpdb->escape($controls->data['search_text']) . "%' or name like '%" . $wpdb->escape($controls->data['search_text']) . "%')";
  }

  if (!empty($controls->data['search_list'])) {
    $query .= " and list_" . ((int) $controls->data['search_list']) . "=1";
  }

  if (!empty($controls->data['search_link'])) {
    list($newsletter, $url) = explode('|', $link);
    $query .= " and id in (select distinct user_id from " . $wpdb->prefix . "newsletter_stats where newsletter='" .
            $wpdb->escape($newsletter) . "' and url='" . $wpdb->escape($url) . "')";
  }

  $query .= ' order by ' . $order;

  if (!empty($options['search_limit'])) {
    $query .= ' limit ' . $limit;
  }

  //if (empty($link)) $query .= ' limit 100';


  $list = $wpdb->get_results($query);

}
else {
    $list = array();
}

?>

<div class="wrap">

    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <?php $controls->show(); ?>

    <form id="channel" method="post" action="">
        <?php $controls->init(); ?>
        <input type="hidden" name="options[subscriber_id]"/>
        <input type="hidden" name="options[subscriber_status]"/>

        <?php
            $tmp = $wpdb->get_results("select distinct newsletter, url from " . $wpdb->prefix . "newsletter_stats order by newsletter,url");
            $links = array(''=>'Unfiltered');
            foreach ($tmp as $t) {
                $links[$t->newsletter . '|' . $t->url] = $t->newsletter . ': ' . substr($t->url, 0, min(strlen($t->url), 50)) . '...';
            }
        ?>

        <div style="padding: .6em; border: 1px solid #ddd; background-color: #f4f4f4; border-radius: 3px;">
        <?php $controls->text('search_text', 80); ?>
        <?php $controls->button('search', 'Search'); ?>
        </div>

        <table class="form-table">
            <tr valign="top">
                <td>
                    <?php $controls->select('search_status', array(''=>'Any status', 'C'=>'Confirmed', 'S'=>'Not confirmed', 'U'=>'Unsubscribed', 'B'=>'Bounced')); ?>
                    <?php
                        $order_fields = array('id'=>'Order by id', 'email'=>'Order by email', 'name'=>'Order by name');
                        for ($i=1; $i<20; $i++) {
                            if ($options_profile['profile_' . $i] == '') continue;
                            $order_fields['profile_' . $i] = $options_profile['profile_' . $i];
                        }
                    ?>

                    <?php $controls->select('search_order', $order_fields); ?>

                    <?php $controls->select('search_limit', array(100=>'Max 100 results', '1000'=>'Max 1000 result', ''=>'No limit')); ?>

                    <?php $controls->select('search_list', $lists); ?>
                    <?php $controls->checkbox('search_test'); ?> Test subscribers
                    <br />
                    <?php _e('show clicks', 'newsletter'); ?>:&nbsp;<?php $controls->yesno('search_clicks'); ?>
                    who&nbsp;clicked:&nbsp;&nbsp;
                    <?php $controls->select('search_link', $links); ?>

                    <div class="hints">
                    Press without filter to show all. Max 100 results will be shown. Use export panel to get all subscribers.
                    </div>
                </td>
            </tr>
            <tr valign="top">
              <td>
                <?php $controls->checkbox('show_profile', 'Show profile fields'); ?>
                <?php $controls->checkbox('show_preferences', 'Show preferences'); ?>
              </td>
            </tr>
        </table>


<h3>Search results</h3>

<?php if (empty($list)) { ?>
<p>No search results (or you did not search at all)</p>
<?php } ?>


<?php if (!empty($list)) { ?>

<table class="widefat">
    <thead>
<tr>
    <th>Id</th>
    <th>Email/Name</th>
    <?php if ($options['show_profile'] == 1) { ?>
      <th>Profile</th>
    <?php } ?>
    <th>Status</th>
    <?php if ($options['show_preferences'] == 1) { ?>
      <th>Preferences</th>
    <?php } ?>
    <th>Actions</th>
    <th>Others</th>
    <?php if ($options['search_clicks'] == 1) { ?>
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


<?php if ($options['show_profile'] == 1) { ?>
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
            case 'S': echo 'NOT CONFIRMED'; break;
            case 'C': echo 'CONFIRMED'; break;
            case 'U': echo 'UNSUBSCRIBED'; break;
            case 'B': echo 'BOUNCED'; break;
        }
        ?>
        <br />
        Feed: <?php echo ($s->feed!=1?'NO':'YES'); ?><br />
        Follow Up: <?php echo ($s->followup!=1?'NO':'YES'); ?> (<?php echo $s->followup_step; ?>)
    </small>
</td>

<?php if ($options['show_preferences'] == 1) { ?>
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
    <a class="button-secondary" href="admin.php?page=newsletter_users_edit&amp;id=<?php echo $s->id; ?>">Edit</a>
    <?php $controls->button_confirm('remove', 'Remove', 'Proceed?', $s->id); ?>

    <?php //$controls->button('status', 'Confirm', 'newsletter_set_status(this.form,' . $s->id . ',\'C\')'); ?>
    <?php //$controls->button('status', 'Unconfirm', 'newsletter_set_status(this.form,' . $s->id . ',\'S\')'); ?>

    <?php $controls->button_confirm('resend', 'Resend confirmation', 'Proceed?', $s->id); ?>
    <?php $controls->button_confirm('resend_welcome', 'Resend welcome', 'Proceed?', $s->id); ?>
    <a href="<?php echo plugins_url('newsletter/do/profile.php'); ?>?nk=<?php echo $s->id . '-' . $s->token; ?>" class="button" target="_blank">Profile page</a>
</td>
<td><small>
        date: <?php echo $s->created; ?><br />

</small></td>

<?php if ($options['search_clicks'] == 1) { ?>
    <td><small>
    <?php
    $clicks = $wpdb->get_results($wpdb->prepare("select * from " . NEWSLETTER_STATS_TABLE . " where user_id=%d order by newsletter", $s->id));
    foreach ($clicks as &$click) {
    ?>
    <?php echo $click->newsletter; ?>: <?php echo $click->url; ?><br />
    <?php } ?>
    </small></td>
<?php } ?>

</tr>
<?php } ?>
</table>
<?php } ?>
    </form>
</div>
