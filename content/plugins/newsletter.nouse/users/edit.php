<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$id = (int) $_GET['id'];

if ($controls->is_action('save')) {

    $email = $module->normalize_email($controls->data['email']);
    if ($email == null) {
        $controls->errors = 'The email address is not valid';
    }

    if (empty($controls->errors)) {
        $user = $module->get_user($controls->data['email']);
        if ($user && $user->id != $id) {
            $controls->errors = 'The email address is already taken by another subscriber';
        }
    }

    if (empty($controls->errors)) {
        // For unselected preferences, force the zero value
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (!isset($controls->data['list_' . $i])) {
                $controls->data['list_' . $i] = 0;
            }
        }

        $controls->data['id'] = $id;
        $r = $module->save_user($controls->data);
        if ($r === false) {
            $controls->errors = 'Unable to update, may be the email (if changed) is duplicated.';
        } else {
            $controls->messages = 'Updated.';
            $controls->data = $module->get_user($id, ARRAY_A);
        }
    }
}

if ($controls->is_action('delete')) {
    $module->delete_user($id);
    $controls->js_redirect($module->get_admin_page_url('index'));
    return;
}

if (!$controls->is_action()) {
    $controls->data = $module->get_user($id, ARRAY_A);
}

$options_profile = get_option('newsletter_profile');
?>
<div class="wrap">
    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <div id="newsletter-title">
        <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

        <h2>Subscriber Edit</h2>
    </div>
    <div class="newsletter-separator"></div> 

    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-general">General</a></li>
                <li><a href="#tabs-preferences">Preferences</a></li>
                <li><a href="#tabs-profile">Profile</a></li>
                <li><a href="#tabs-other">Other</a></li>
            </ul>

            <div id="tabs-general">

                <table class="form-table">
                    <tr valign="top">
                        <th>Email address</th>
                        <td>
                            <?php $controls->text('email', 60); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>First name</th>
                        <td>
                            <?php $controls->text('name', 50); ?>
                            <div class="hints">
                                If you collect only the name of the subscriber without distinction of first and last name this field is used.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Last name</th>
                        <td>
                            <?php $controls->text('surname', 50); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Gender</th>
                        <td>
                            <?php $controls->select('sex', array('n' => 'Not specified', 'f' => 'female', 'm' => 'male')); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Status</th>
                        <td>
                            <?php $controls->select('status', array('C' => 'Confirmed', 'S' => 'Not confirmed', 'U' => 'Unsubscribed', 'B' => 'Bounced')); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Test subscriber?</th>
                        <td>
                            <?php $controls->yesno('test'); ?>
                            <div class="hints">
                                A test subscriber is a regular subscriber that is even used as recipint when sending test newsletter are sent
                                (for example to check the layout).
                            </div>
                        </td>
                    </tr>

                    <?php do_action('newsletter_user_edit_extra', $controls); ?>

                    <tr valign="top">
                        <th>Feed by mail</th>
                        <td>
                            <?php $controls->yesno('feed'); ?>
                            <div class="hints">
                                "Yes" when this subscriber has the feed by mail service active. The 
                                <a href="http://www.thenewsletterplugin.com/feed-by-mail-extension?utm_source=plugin&utm_medium=link&utm_campaign=newsletter-feed" target="_blank">feed by mail is an extension of this plugin</a>.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="tabs-preferences">
                <div class="tab-preamble">
                    <p>
                        The subscriber's preferences are a set of "on/off" fields that can be used while sending newsletter to
                        select a subset of subscribes. 
                    </p>
                </div>
                <table class="form-table">
                    <tr>
                        <th>Preferences</th>
                        <td>
                            <?php $controls->preferences('list'); ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="tabs-profile">
                <div class="tab-preamble">
                    <p>
                        The user's profile is a set of optional and textual fields you can collect along with the subscription process
                        or when the user's editing his profile. Those fields can be configured in the "Subscription Form" panel.
                    </p>
                </div>
                <table class="widefat" style="width:auto">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
                            echo '<tr><td>(' . $i . ') ';
                            echo '</td><td>';
                            echo $options_profile['profile_' . $i];
                            echo '</td><td>';
                            $controls->text('profile_' . $i, 70);
                            echo '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="tabs-other">
                <div class="tab-preamble">
                    <p>
                        Other user's data collected or generated along the subscription process.
                    </p>
                </div>
                <table class="form-table">
                    <tr valign="top">
                        <th>Subscriber ID</th>
                        <td>
                            <?php $controls->value('id'); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Created</th>
                        <td>
                            <?php $controls->value('created'); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>From IP address</th>
                        <td>
                            <?php $controls->value('ip'); ?>
                            <div class="hints">
                                Internet address from which the subscription started. Required by many providers.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Secret token</th>
                        <td>
                            <?php $controls->text('token', 50); ?>
                            <div class="hints">
                                This secret token is used to access the profile page and edit profile data, to confirm and cancel the subscription.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Profile URL</th>
                        <td>
                            <?php echo plugins_url('newsletter/do/profile.php') . '?nk=' . $id . '-' . $controls->data['token']; ?>
                            <div class="hints">
                                The URL which lands on the user profile editing page. It can be added on newsletters using the {profile_url} tag.
                            </div>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <p class="submit">
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button('delete', 'Delete'); ?>
        </p>

    </form>
</div>
