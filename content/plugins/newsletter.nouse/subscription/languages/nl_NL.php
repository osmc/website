<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Je hebt je ingeschreven op de nieuwsbrief.</p>
<p>Binnen enkele minuten zal je een bevestigingsmail ontvangen. Volg de link in die mail om jouw inschrijving te bevestigen. Indien je problemen hebt met het ontvangen van de bevestigingsmail kan je ons via het contactformulier bereiken.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"{name}, Bevestig jouw inschrijving op de nieuwsbrief van {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Hallo {name},</p>
<p>We ontvingen jouw inschrijving op onze nieuwsbrief. Gelieve de inschrijving te bevestigen door <a href=\"{subscription_confirm_url}\"><strong>hier</strong></a> te klikken. Als het klikken op de link voor jou niet werkt, kan je de volgende link in jouw browser copieren.</p>
<p>{subscription_confirm_url}</p>
<p>Indien je deze mail ontvangt en toch geen inschrijving gevraagd hebt, hoef je niets te doen. De inschrijving wordt dan automatisch geannuleerd.</p>
<p>Dank u wel.</p>";

// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Je hebt zonet jouw inschrijving bevestigd.</p><p>bedankt {name} !</p>";

$options['confirmed_subject'] =
"Welkom, {name}";

$options['confirmed_message'] =
"<p>Uw inschrijving op de niewsbrief van {blog_title} is bevestigd.</p>
<p>Bedankt !</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Gelieve uw uitschrijving te bevestigen door <a href=\"{unsubscription_confirm_url}\">hier</a> te klikken.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>U bent uit onze lijst verwijderd.</p>";

$options['unsubscribed_subject'] =
"Tot ziens, {name}";

$options['unsubscribed_message'] =
"<p>Uw uitschrijving op de nieuwsbrief van {blog_title} is bevestigd.</p>
<p>Tot ziens.</p>";


