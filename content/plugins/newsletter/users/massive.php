<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$options_profile = get_option('newsletter_profile');

$lists = array();
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    if (!isset($options_profile['list_' . $i])) $options_profile['list_' . $i] = '';
  $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}

if ($controls->is_action('remove_unconfirmed')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='S'");
  $controls->messages = __('Subscribers not confirmed deleted: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('remove_unsubscribed')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='U'");
  $controls->messages = __('Subscribers unsubscribed deleted: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('remove_bounced')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='B'");
  $controls->messages = __('Subscribers bounced deleted: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('unconfirm_all')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='S' where status='C'");
  $controls->messages = __('Subscribers changed to not confirmed: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('confirm_all')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='C' where status='S'");
  $controls->messages = __('Subscribers changed to confirmed: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('remove_all')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE);
  $controls->messages = __('Subscribers deleted: ', 'newsletter-users') . $r . '.';
}

if ($controls->is_action('list_add')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['list'] . "=1");
  $controls->messages = $r . ' ' . __('added to the list/preference', 'newsletter-users') . ' ' . $controls->data['list'];
}

if ($controls->is_action('list_remove')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['list'] . "=0");
  $controls->messages = $r . ' ' . __('removed to the list/preference', 'newsletter-users') . ' ' . $controls->data['list'];
}

if ($controls->is_action('list_delete')) {
  $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where list_" . $controls->data['list'] . "<>0");
}

if ($controls->is_action('list_manage')) {
  if ($controls->data['list_action'] == 'move') {
    $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . $controls->data['list_1'] . '=0, list_' . $controls->data['list_2'] . '=1' .
            ' where list_' . $controls->data['list_1'] . '=1');
  }

  if ($controls->data['list_action'] == 'add') {
    $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . $controls->data['list_2'] . '=1' .
            ' where list_' . $controls->data['list_1'] . '=1');
  }
}

if ($controls->is_action('resend_all')) {
    $list = $wpdb->get_results("select * from " . NEWSLETTER_USERS_TABLE . " where status='S'");
    $opts = get_option('newsletter');

    if ($list) {
        $controls->messages = __('Confirmation email sent to:', 'newsletter-users');
        foreach ($list as &$user) {
            $controls->messages .= $user->email . ' ';
            $newsletter->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
        }
    } else {
        $controls->errors = 'No subscribers to which rensend the confirmation email';
    }

}

if ($controls->is_action('align_wp_users')) {

    // TODO: check if the user is already there
    $wp_users = $wpdb->get_results("select id, user_email, user_login from $wpdb->users");
    $count = 0;
    foreach ($wp_users as &$wp_user) {
        $module->logger->info('Adding a registered WordPress user (' . $wp_user->id . ')');

        // A subscriber is already there with the same wp_user_id? Do Nothing.
        $nl_user = $module->get_user_by_wp_user_id($wp_user->id);
        if (!empty($nl_user)) {
            $module->logger->info('Subscriber already associated');
            continue;
        }

        $module->logger->info('WP user email: ', $wp_user->user_email);

        // A subscriber has the same email? Align them if not already associated to another wordpress user
        $nl_user = $module->get_user($module->normalize_email($wp_user->user_email));
        if (!empty($nl_user)) {
            $module->logger->info('Subscriber already present with that email');
            if (empty($nl_user->wp_user_id)) {
                $module->logger->info('Linked');
                $module->set_user_wp_user_id($nl_user->id, $wp_user->id);
                continue;
            }
        }

        $module->logger->info('New subscriber created');

        // Create a new subscriber
        $nl_user = array();
        $nl_user['email'] = $module->normalize_email($wp_user->user_email);
        $nl_user['name'] = $wp_user->user_login;
        $nl_user['status'] = $controls->data['align_wp_users_status'];
        $nl_user['wp_user_id'] = $wp_user->id;
        $nl_user['referrer'] = 'wordpress';

        // Adds the force subscription preferences
        $preferences = NewsletterSubscription::instance()->options['preferences'];
        if (is_array($preferences)) {
            foreach ($preferences as $p) {
                $nl_user['list_' . $p] = 1;
            }
        }

        $module->save_user($nl_user);
        $count++;
    }
    $controls->messages = count($wp_users) . ' ' . __('WordPress users processed', 'newsletter-users') . '. ';
    $controls->messages .= $count  . ' ' . __('subscriptions added', 'newsletter-users') . '.';
}


if ($controls->is_action('bounces')) {
    $lines = explode("\n", $controls->data['bounced_emails']);
    $total = 0;
    $marked = 0;
    $error = 0;
    $not_found = 0;
    $already_bounced = 0;
    $results = '';
    foreach ($lines as &$email) {
        $email = trim($email);
        if (empty($email)) continue;

        $total++;

        $email = NewsletterModule::normalize_email($email);
        if (empty($email)) {
              $results .= '[INVALID] ' . $email . "\n";
          $error++;
            continue;
        }

        $user = NewsletterUsers::instance()->get_user($email);

        if ($user == null) {
          $results .= '[NOT FOUND] ' . $email . "\n";
          $not_found++;
          continue;
        }

        if ($user->status == 'B') {
          $results .= '[ALREADY BOUNCED] ' . $email . "\n";
          $already_bounced++;
          continue;
        }

        $r = NewsletterUsers::instance()->set_user_status($email, 'B');
        if ($r === 0) {
          $results .= '[BOUNCED] ' . $email . "\n";
        $marked++;
          continue;
        }
    }

    $controls->messages .= 'Total: ' . $total . '<br>';
    $controls->messages .= 'Bounce: ' . $marked . '<br>';
    $controls->messages .= 'Errors: ' . $error . '<br>';
    $controls->messages .= 'Not found: ' . $not_found . '<br>';
    $controls->messages .= 'Already bounced: ' . $already_bounced . '<br>';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>
  

    <div id="newsletter-title">
        <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>
    <h2>Massive Actions on Subscribers</h2>
    <p><?php _e('Please, backup before run a massive action.', 'newsletter-users')?></p>
    </div>

  <div style="clear: both; height: 10px;"></div>
  <?php $controls->show(); ?>

    <?php if (!empty($results)) { ?>

    <h3>Results</h3>

    <textarea wrap="off" style="width: 100%; height: 150px; font-size: 11px; font-family: monospace"><?php echo htmlspecialchars($results) ?></textarea>

    <?php } ?>


  <form method="post" action="">
  <?php $controls->init(); ?>

    <div id="tabs">
      <ul>
        <li><a href="#tabs-1"><?php _e('Massive actions', 'newsletter-users')?></a></li>
        <li><a href="#tabs-2"><?php _e('Preferences/lists management', 'newsletter-users')?></a></li>
        <li><a href="#tabs-3"><?php _e('Other', 'newsletter-users')?></a></li>
        <li><a href="#tabs-4">Bounces</a></li>
      </ul>

      <div id="tabs-1">
        <table class="widefat" style="width: 300px;">
          <thead>
              <tr>
                  <th><?php _e('Status', 'newsletter-users')?></th>
                  <th><?php _e('Total', 'newsletter-users')?></th>
                  <th><?php _e('Actions', 'newsletter-users')?></th>
              </tr>
          </thead>
          <tr>
            <td><?php _e('Total collected emails', 'newsletter-users')?></td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('remove_all', __('Delete all', 'newsletter-users'), __('Are you sure you want to remove ALL subscribers?', 'newsletter-users')); ?>
            </td>
          </tr>
          <tr>
            <td><?php _e('Confirmed', 'newsletter-users')?></td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'"); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('unconfirm_all', __('Unconfirm all', 'newsletter-users'), __('Are you sure?', 'newsletter-users')); ?>
            </td>
          </tr>
          <tr>
            <td>Not confirmed</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='S'"); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('remove_unconfirmed', __('Delete all not confirmed', 'newsletter-users'), __('Are you sure you want to delete ALL not confirmed subscribers?', 'newsletter-users')); ?>
              <?php $controls->button_confirm('confirm_all', __('Confirm all', 'newsletter-users'), __('Are you sure you want to mark ALL subscribers as confirmed?', 'newsletter-users')); ?>
                <p class="description">
                    <a href="http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#resend-confirm" target="_blank"><?php _e('We have some tips about global actions, read more.', 'newsletter-users')?></a>
                </p>
            </td>
          </tr>
          <tr>
            <td><?php _e('Unsubscribed', 'newsletter-users')?></td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='U'"); ?>
            </td>
            <td>
              <?php $controls->button_confirm('remove_unsubscribed', __('Delete all unsubscribed', 'newsletter-users'), __('Are you sure?', 'newsletter-users')); ?>
            </td>
          </tr>
          <tr>
            <td><?php _e('Import WordPress users', 'newsletter-users')?></td>
            <td>
                &nbsp;
            </td>
            <td>
                <?php _e('With status', 'newsletter-users')?>
                <?php $controls->select('align_wp_users_status', array('C'=>__('Confirmed', 'newsletter-users'), 'S'=>__('Not confirmed', 'newsletter-users'))); ?>
                <?php $controls->button_confirm('align_wp_users', __('Go', 'newsletter-users'), __('Proceed?', 'newsletter-users')); ?>
                <p class="description">
                    <a href="http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#import-wp-users" target="_blank">
                        <?php _e('Please, carefully read the documentation before taking this action!', 'newsletter-users') ?>
                    </a>
                </p>
            </td>
          </tr>

          <tr>
            <td><?php _e('Bounced', 'newsletter-users')?></td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='B'"); ?>
            </td>
            <td>
              <?php $controls->button_confirm('remove_bounced', __('Delete all bounced', 'newsletter-users'), __('Are you sure?', 'newsletter-users')); ?>
            </td>
          </tr>
        </table>
        <p>Bounce are not detected by Newsletter plugin.</p>

        <h3><?php _e('Gender', 'newsletter-users')?></h3>
        <?php
            // TODO: do them with a single query
            $all_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'");
            $male_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where sex='m' and status='C'");
            $female_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where sex='f' and status='C'");
            $other_count = ($all_count-$male_count-$female_count)
        ?>
        <table class="widefat" style="width: 300px">
            <thead><tr><th><?php _e('Gender', 'newsletter-users')?></th><th>Total</th></thead>
            <tr><td>Male</td><td><?php echo $male_count; ?></td></tr>
            <tr><td>Female</td><td><?php echo $female_count; ?></td></tr>
            <tr><td>Not specified</td><td><?php echo $other_count; ?></td></tr>
        </table>
      </div>


      <div id="tabs-2">
        <table class="form-table">
          <tr>
            <th><?php _e('Preferences/lists management', 'newsletter-users')?></th>
            <td>
              For preference <?php $controls->select('list', $lists); ?>:
              <?php $controls->button_confirm('list_add', 'Add it to every user', __('Are you sure?', 'newsletter-users')); ?>
              <?php $controls->button_confirm('list_remove', 'Remove it from every user', __('Are you sure?', 'newsletter-users')); ?>
              <?php $controls->button_confirm('list_delete', 'Delete subscribers of it', __('Are you sure?', 'newsletter-users')); ?>
              <br /><br />
              <?php $controls->select('list_action', array('move' => 'Change', 'add' => 'Add')); ?>
              all subscribers with preference <?php $controls->select('list_1', $lists); ?>
              to preference <?php $controls->select('list_2', $lists); ?>
              <?php $controls->button_confirm('list_manage', 'Go!', 'Are you sure?'); ?>
              <div class="hints">
                If you choose to <strong>delete</strong> users in a list, they will be
                <strong>physically deleted</strong> from the database (no way back).
              </div>
            </td>
          </tr>
        </table>
      </div>


      <div id="tabs-3">
        <p><?php _e('Totals refer only to confirmed subscribers.', 'newsletter-users')?></p>
        <table class="widefat" style="width: 300px;">
          <thead>
              <tr>
                  <th><?php _e('Number', 'newsletter-users')?></th>
                  <th><?php _e('Preference', 'newsletter-users')?></th>
                  <th><?php _e('Total', 'newsletter-users')?></th>
              </tr>
          </thead>
          <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
            <tr>
              <td><?php echo $i; ?></td>
              <td><?php echo $options_profile['list_' . $i]; ?></td>
              <td>
                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $i . "=1 and status='C'"); ?>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>

        <div id="tabs-4">
            <p>
                Import a set of bounced email addresses: they will be marked as "bounced" and no more contacted. Sending
                emails to bounced address (many times) can put your server in some black list.
            </p>

            <table class="form-table">
                <tr>
                    <th><?php _e('Bounced addresses', 'newsletter-users')?></th>
                    <td>
                        <?php $controls->textarea('bounced_emails'); ?>
                        <p class="description">
                            <?php _e('One email address per line.', 'newsletter-users')?>One email address per line.
                        </p>
                    </td>
                </tr>
            </table>

            <?php $controls->button_confirm('bounces', 'Mark those emails as bounced', __('Are you sure?', 'newsletter-users')); ?>
        </div>

    </div>

  </form>
  </div>
