<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Votre demande d'inscription est enregistr&eacute;e. Merci ! Un e-mail de confirmation vous a été envoyé. V&eacute;rifier &eacute;galement votre dossier spam.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"Inscription &agrave; la lettre d&prime;information de {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Bonjour {name}!</p>
<p>Vous recevez cet e-mail car nous avons enregistr&eacute; une demande d&prime;inscription &agrave; la lettre d&prime;infrmation de {blog_title}.
Merci de confirmer votre inscription en cliquant sur le lien suivant : <a href=\"{subscription_confirm_url}\"><strong>je confirme mon inscription.</strong></a>.
Si le lien ne fonctionne pas, merci d'utiliser cette adresse :</p>
<p>{subscription_confirm_url}</p>
<p>Ignorez ce message si vous n'avez pas effectué de demande.</p>
<p>Merci !</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Votre inscription est confirm&eacute;e. Merci !</p>";

$options['confirmed_subject'] =
"Bienvenue, {name}";

$options['confirmed_message'] =
"<p>Votre inscription est confirm&eacute;e. Merci !</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>&Ecirc;tes-vous certain de vouloir vous d&eacute;sinscrire de la lettre d&prime;information de {blog_title} ? <a href=\"{unsubscription_confirm_url}\">Oui</a>.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>Vous n&prime;&ecirc;tes plus abonn&eacute; &agrave; la lettre d'information de {blog_title}. Merci de nous avoir suivi et &agrave; bient&ocirc;t !</p>";



