<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_profile');
} else {
    if ($controls->is_action('save')) {
        update_option('newsletter_profile', $controls->data);
    }

    if ($controls->is_action('reset')) {
        // TODO: Move this inside the module
        @include NEWSLETTER_DIR . '/subscription/languages/profile-en_US.php';
        @include NEWSLETTER_DIR . '/subscription/languages/profile-' . WPLANG . '.php';
        update_option('newsletter_profile', array_merge(get_option('newsletter_profile', array()), $options));
        $controls->data = get_option('newsletter_profile');
    }
}

$status = array(0 => 'Disabled/Private use', 1 => 'Only on profile page', 2 => 'Even on subscription forms');
$rules = array(0 => 'Optional', 1 => 'Required');
?>

<div class="wrap">
    <?php $help_url = 'http://www.thenewsletterplugin.com/plugins/newsletter/subscription-module'; ?>
    <?php include NEWSLETTER_DIR . '/header-new.php'; ?>

    <div id="newsletter-title">
        <?php include NEWSLETTER_DIR . '/subscription/menu.inc.php'; ?>

        <h2>Subscription Form Fields and Layout</h2>
        <p>
            This panel contains the configuration of the subscription and profile editing forms which collect the subscriber data you want to have.<br>
            And let you to <strong>translate</strong> every single button and label.<br>
            <strong>Preferences</strong> can be an important setting for your newsletter: <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">here you can read more about them</a>.
        </p>

    </div>
    <div class="newsletter-separator"></div>


    <?php $controls->show(); ?>
    <form action="" method="post">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-2">Main profile fields</a></li>
                <li><a href="#tabs-3">Extra profile fields</a></li>
                <li><a href="#tabs-4">Preferences</a></li>
                <li><a href="#tabs-5">Form code</a></li>
                <li><a href="#tabs-6">Form style</a></li>
            </ul>

            <div id="tabs-2">

                <p>The main subscriber fields. Only the email field is, of course, mandatory.</p>

                <table class="form-table">
                    <tr>
                        <th>Email</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Field label</th><td><?php $controls->text('email', 50); ?></td></tr>
                                <tr><th>Error message</th><td><?php $controls->text('email_error', 50); ?></td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>Name or first name</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Field label</th><td><?php $controls->text('name', 50); ?></td></tr>
                                <tr><th>When to show</th><td><?php $controls->select('name_status', $status); ?></td></tr>
                                <tr><th>Rules</th><td><?php $controls->select('name_rules', $rules); ?></td></tr>
                                <tr><th>Error message</th><td><?php $controls->text('name_error', 50); ?></td></tr>
                            </table>
                            <p class="description">
                                If you want to collect only a generic "name", use only this field and not the
                                last name field.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Last name</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Field label</th><td><?php $controls->text('surname', 50); ?></td></tr>
                                <tr><th>When to show</th><td><?php $controls->select('surname_status', $status); ?></td></tr>
                                <tr><th>Rules</th><td><?php $controls->select('surname_rules', $rules); ?></td></tr>
                                <tr><th>Error message</th><td><?php $controls->text('surname_error', 50); ?></td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>Sex/Gender</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Field label</th><td><?php $controls->text('sex', 50); ?></td></tr>
                                <tr><th>When to show</th><td><?php $controls->select('sex_status', $status); ?></td></tr>
                                <tr><th>Value labels</th><td>
                                        female: <?php $controls->text('sex_female'); ?>
                                        male: <?php $controls->text('sex_male'); ?>
                                        not specified: <?php $controls->text('sex_none'); ?>
                                    </td></tr>

                                <tr><th>Salutation titles</th><td>

                                        for males: <?php $controls->text('title_male'); ?> (ex. "Mr")<br>
                                        for females: <?php $controls->text('title_female'); ?> (ex. "Mrs")<br>
                                        for others: <?php $controls->text('title_none'); ?>
                                    </td></tr>
                            </table>
                            <p class="description">
                                Salutation titles are inserted in emails message when the tag {title} is used. For example
                                "Good morning {title} {surname} {name}".
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th>Button labels</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Subscribe button</th><td><?php $controls->text('subscribe'); ?></td></tr>
                                <tr><th>Save button</th><td><?php $controls->text('save'); ?> (on profile page)</td></tr>
                            </table>
                            <p class="description">
                                For "subscribe" insert an URL to an image (http://...) to use it as a graphical button.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th>Privacy check box</th>
                        <td>
                            <table class="newsletter-option-grid">
                                <tr><th>Enabled?</th><td><?php $controls->yesno('privacy_status'); ?></td></tr>
                                <tr><th>Label</th><td><?php $controls->text('privacy', 50); ?></td></tr>
                                <tr><th>Privacy URL</th><td><?php $controls->text('privacy_url', 50); ?></td></tr>
                                <tr><th>Error message</th><td><?php $controls->text('privacy_error', 50); ?></td></tr>
                            </table>
                            <p class="description">
                                The privacy acceptance checkbox (required in many Europen countries) force the subscriber to
                                check it to procees. If an URL is specified the label is linket to that page.
                            </p>
                        </td>
                    </tr>

                </table>
            </div>


            <div id="tabs-3">
                <p>
                    Generic textual profile fields that can be collected during the subscription. Field formats can be one line text
                    or selection list. Fields of type "list" must be configured with a set of options, comma separated
                    like: "first option, second option, third option".
                </p>
                <p>
                    The placeholder works only on HTML 5 compliant browsers.
                </p>
                
                 <table class="form-table">
                    <tr>
                        <th>Error message</th>
                        <td>
                            <?php $controls->text('profile_error', 50); ?>
                        </td>
                    </tr>
                </table>

                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Field</th><th>Name/Label</th><th>Placeholder</th><th>When/Where</th><th>Type</th><th>Rule</th><th>List values comma separated</th>
                        </tr>
                    </thead>
                    <?php for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) { ?>
                        <tr>
                            <td>Profile <?php echo $i; ?></td>
                            <td><?php $controls->text('profile_' . $i); ?></td>
                            <td><?php $controls->text('profile_' . $i . '_placeholder'); ?></td>
                            <td><?php $controls->select('profile_' . $i . '_status', $status); ?></td>
                            <td><?php $controls->select('profile_' . $i . '_type', array('text' => 'Text', 'select' => 'List')); ?></td>
                            <td><?php $controls->select('profile_' . $i . '_rules', $rules); ?></td>
                            <td>
                                <?php $controls->textarea_fixed('profile_' . $i . '_options', '200px', '50px'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

            </div>


            <div id="tabs-4">
                <p>
                    Preferences are on/off choices users can select during subscription and change on their profile.
                    Those preferences are then used by you to target emails you create. Private preferenced can be used
                    to create group/list since the subscriber cannot change them.
                </p>

                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Name/Label</th>
                            <th>When/Where</th>
                            <th>Initially...</th>
                        </tr>
                    </thead>
                    <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
                        <tr>
                            <td>Preference <?php echo $i; ?></td>
                            <td><?php $controls->text('list_' . $i); ?></td>
                            <td><?php $controls->select('list_' . $i . '_status', $status); ?></td>
                            <td><?php $controls->select('list_' . $i . '_checked', array(0 => 'Unchecked', 1 => 'Checked')); ?></td>
                        </tr>
                    <?php } ?>
                </table>

            </div>


            <div id="tabs-5">

                <p>This panel shows the form HTML code generated by Newsletter if you want to copy it as starting point for a custom form.</p>

                <h3>Standard form code</h3>
                <textarea readonly style="width: 100%; height: 500px; font-family: monospace"><?php echo htmlspecialchars(NewsletterSubscription::instance()->get_subscription_form()); ?></textarea>

                <h3>Widget form code</h3>
                <textarea readonly style="width: 100%; height: 500px; font-family: monospace"><?php echo htmlspecialchars(NewsletterSubscription::instance()->get_subscription_form()); ?></textarea>

            </div>

            <div id="tabs-6">

                <table class="form-table">
                    <tr>
                        <th>Subscription form style</th>
                        <td>
                            <?php $controls->select('style', $module->get_styles()); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Widget style</th>
                        <td>
                            <?php $controls->select('widget_style', $module->get_styles()); ?>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <p>
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button_confirm('reset', 'Reset all', 'Are you sure you want to reset all?'); ?>
        </p>

    </form>
</div>