<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Zostałeś zapisany do subskrypcji.
W ciągu kilku minut otrzymasz e-mail potwierdzający.
Kliknij w odnośnik w nim zawarty aby potwierdzić subskrypcję. Jeśli e-mail nie pojawi się w Twojej skrzynce przez 15 minut - sprawdź folder spam.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"{name}, potwierdź swoją subskrypcję w {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Witaj {name},</p>
<p>Otrzymaliśmy prośbę o wpis do subskrypcji dla tego adresu e-mail. Możesz potwierdzić ją
<a href=\"{subscription_confirm_url}\"><strong>klikając tutaj</strong></a>.
Jeśli nie możesz kliknąć odnośnika, użyj poniższego linku:</p>
<p>{subscription_confirm_url}</p>
<p>Jeśli to nie Ty wpisywałeś się do subskrypcji, po prostu zignoruj tę wiadomość.</p>
<p>Dziękujemy.</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Twoja subskrypcja została potwierdzona!
Dziękujemy {name}!</p>";

$options['confirmed_subject'] =
"Witaj, {name}";

$options['confirmed_message'] =
"<p>Wiadomość potwierdzająca subskyrpcję {blog_title}.</p>
<p>Dziękujemy!</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Proszę potwierdzić rezygnację z subskrypcji
<a href=\"{unsubscription_confirm_url}\">klikając tutaj</a>.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>To smutne, ale usunęliśmy Twój e-mail z subskrypcji...</p>";


