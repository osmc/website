<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Você foi inscrito corretamente na newsletter.
Em alguns minutos você receberá um email de confirmação. Siga o link para confirmar a inscrição.
Se o email demorar mais do que 15 minutos para chegar, cheque sua caixa de SPAM.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"{name}, confirme sua inscrição no site {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Oi {name},</p>
<p>Recebemos um pedido de inscrição nos nossos informativos deste email. Você pode confirmar
<a href=\"{subscription_confirm_url}\"><strong>clicando aqui</strong></a>.
Se você não puder seguir o link, acesse este endereço:</p>
<p>{subscription_confirm_url}</p>
<p>Se o pedido de inscrição não veio de você, apenas ignore esta mensagem.</p>
<p>Obrigado.</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Sua inscrição foi confirmada!
Obrigado {name}.</p>";

$options['confirmed_subject'] =
"Bem vindo(a) a bordo, {name}";

$options['confirmed_message'] =
"<p>A mensagem confirma a sua inscrição nos nossos informativos.</p>
<p>Obrigado.</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Cancele a sua inscrição nos informativos
<a href=\"{unsubscription_confirm_url}\">clicando aqui</a>.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>Sua inscrição foi cancelada. Inscreva-se novamente quando quiser.</p>";



?>
