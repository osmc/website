<?php
// Those default options are used ONLY on FIRST setup and on plugin updates but limited to
// new options that may have been added between your and new version.
//
// This is the main language file, too, which is always loaded by Newsletter. Other language
// files are loaded according the WPLANG constant defined in wp-config.php file. Those language
// specific files are "merged" with this one and the language specific configuration
// keys override the ones in this file.
//
// Language specific files only need to override configurations containing texts
// langiage dependant.

$options = array();

// Profile page
$options['profile_text'] = "<p>Change your subscription preferences to get what you are most interested in.</p>
    <p>If you change your email address, a confirmation email will be sent to activate it.</p>
    </p>
    {profile_form}
    <p>To cancel your subscription, <a href='{unsubscription_confirm_url}'>click here</a>.</p>";

// Profile page messages
$options['profile_email_changed'] = "Your email has been changed, an activation email has been sent. Please follow the instructions to activate the new address.";
$options['profile_error'] = "Your email is not valid or already in use by another subscriber or another generic error has been found. Check your data or contact the site owner.";
    
$options['error_text'] = '<p>This subscription can\'t be completed, sorry. The email address is blocked or already subscribed. You should contact the owner to unlock that email address. Thank you.</p>';

$options['already_confirmed_text'] = '<p>This email address is already subscribed, anyway a welcome email has been resent. Thank you.</p>';

$options['subscribe_wp_users'] = 0;

// Subscription page introductory text (befor the subscription form)
$options['subscription_text'] =
"{subscription_form}";

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>You have successfully subscribed to the newsletter. You'll
receive a confirmation email in few minutes. Please follow the
link in it to confirm your subscription. If the email takes
more than 15 minutes to appear in your mailbox, please check
your spam folder.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"Please confirm subscription - {blog_title} newsletter";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Hi {name},</p>
<p>A newsletter subscription request for this email address was
received. Please confirm it by <a href=\"{subscription_confirm_url}\"><strong>clicking here</strong></a>. If you cannot
click the link, please use the following link:</p>

<p>{subscription_confirm_url}</p>

<p>If you did not make this subscription request, just ignore this
message.</p>
<p>Thank you!<br>
<a href='{blog_url}'>{blog_url}</a></p>";



// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Your subscription has been confirmed! Thank you {name}!</p>";

$options['confirmed_subject'] =
"Welcome aboard, {name}";

$options['confirmed_message'] =
"<p>This message confirms your subscription to the {blog_title} newsletter.</p>
<p>Thank you!<br>
<a href='{blog_url}'>{blog_url}</a></p>
<p>To unsubscribe, <a href='{unsubscription_url}'>click here</a>.  To change subscriber options,
<a href='{profile_url}'>click here</a>.</p>";

$options['confirmed_tracking'] = '';
        
// Unsubscription process

$options['unsubscription_text'] = "<p>Please confirm that you want to unsubscribe by <a href='{unsubscription_confirm_url}'>clicking here</a>.</p>";
$options['unsubscription_error_text'] = "<p>The subscriber was not found, it probably has already been removed. No further actions are required. Thank you.</p>";

// When you finally loosed your subscriber
$options['unsubscribed_text'] = "<p>Your subscription has been deleted. Thank you.</p>";

$options['unsubscribed_subject'] = "Goodbye!";

$options['unsubscribed_message'] =
"<p>This message confirms that you have unsubscribed from the {blog_title} newsletter.</p>
<p>You're welcome to sign up again anytime.</p>
<p>Thank you!<br>
<a href='{blog_url}'>{blog_url}</a></p>";
