<?php

// This file is used only on first installation!

// Subscription registration message
$options['confirmation_text'] =
"<p>L'iscrizione è quasi completa: controlla la tua
casella di posta, c'è un messaggio per te con il quale confermare l'iscrizione.</p>";

// Confirmation email (double opt-in)
$options['confirmation_subject'] =
"{name}, conferma l'iscrizione alle newsletter di {blog_title}";

$options['confirmation_message'] =
"<p>Ciao {name},</p>
<p>hai richiesto l'iscrizione alla newsletter di {blog_title}.
Conferma l'iscrizione <a href=\"{subscription_confirm_url}\"><strong>cliccando qui</strong></a>
oppure copia il link qui sotto nel tu programma di navigazione:</p>
<p>{subscription_confirm_url}</p>
<p>Grazie!</p>";

$options['confirmed_subject'] =
"Benvenuto {name}!";

$options['confirmed_message'] =
"<p>Con questo messaggio ti confermo l'iscrizione alla newsletter.</p>
<p>Grazie!</p>";

// Subscription confirmed text
$options['confirmed_text'] =
"<p>{name}, la tua iscrizione è stata confermata.
Buona lettura!</p>";


$options['unsubscription_text'] =
"<p>{name}, vuoi eliminare la tua iscrizione?
Se sì... mi dispace, ma non ti trattengo oltre:</p>
<p><a href=\"{unsubscription_confirm_url}\">Sì, voglio eliminare la mia iscrizione per sempre</a>.</p>";

$options['unsubscribed_text'] =
"<p>La tua iscrizione è stata definitivamente eliminata.</p>";
