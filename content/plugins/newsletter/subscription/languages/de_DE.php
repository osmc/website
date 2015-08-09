<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Sie haben sich in unseren Newsletter eingetragen.
In wenigen Minuten erhalten Sie eine Best&auml;tigungs-Email. Folgen Sie dem Link um die Anmeldung zu best&auml;tigen. Sollte die Email nicht innerhalb der n&auml;chsten 15 Minuten in Ihrem Posteingang erscheinen, &uuml;berpr&uuml;fen Sie Ihren Spam-Ordner.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"{name},{blog_title} Newsletter - hier Anmeldebest&auml;tigung";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Hallo {name},</p>
<p>Für diese Email-Adresse haben wir eine Anmeldung zu unserem Newsletter erhalten. Sie k&ouml;nnen diese Anmeldung best&auml;tigen, in dem Sie <a href=\"{subscription_confirm_url}\"><strong>hier klicken</strong></a>.
Wenn Sie nicht klicken k&ouml;nnen, nutzen Sie die folgenden URL in Ihren Browser ein:</p>
<p>{subscription_confirm_url}</p>
<p>Wenn die Anmeldung zu unserem Newsletter nicht von Ihnen stammt, ignorieren Sie diese Nachricht einfach.</p>
<p>Vielen Dank.</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Ihre Anmeldung zu unserem Newsletter wurde best&auml;tigt!
Herzlichen Dank!</p>";

$options['confirmed_subject'] =
"{blog_title} Newsletter - Willkommen";

$options['confirmed_message'] =
"<p>
Hallo {name},
Willkommen zu unserem {blog_title} Newsletter.</p>
<p>
Wir werden Sie künftig regelm&auml;&szlig;ig &uuml;ber Neuigkeiten zu {blog_title} informieren</p>
<p>
Wenn Sie unseren newsletter nicht mehr erhalten m&ouml;chten, tragen Sie sich bitte unter dem folgenden Link aus dem Verteiler aus: <a href=\"{unsubscription_url}\">austragen</a></p>
<p>Besten Dank!</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Bitte best&auml;tigen Sie, dass Sie unseren Newsletter abbestellen, indem Sie
<a href=\"{unsubscription_confirm_url}\">hier klicken</a>.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>Herzlichen Dank, Sie wurden aus dem Verteiler entfernt...</p>";
