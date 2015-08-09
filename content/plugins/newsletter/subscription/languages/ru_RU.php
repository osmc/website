<?php

// This file is used only on first installation!

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>Вы успешно подписаны на рассылку. Вы получите письмо с подтверждением через несколько минут. Перейдите по ссылке в письме для подтверждения. Если в течении 15 минут письмо все-таки не пришло, проверьте папку со спамом на вашем ящике, на случай если почтовая служба сочла письмо спамом. Если же письма нигде нет, свяжитесь с администратором сайта</a>.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"{name}, Подвердите вашу подписку на новостную ленту {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Здравствуйте, {name},</p>
<p>От Вас поступил запрос на получение новостной рассылки. Вы можете подтвердить его, кликнув на эту <a href=\"{subscription_confirm_url}\"><strong>ссылку</strong></a>. Если ссылка по каким-то причинам не нажимается, вставьте вручную в браузер, ссылку:</p>
<p>{subscription_confirm_url}</p>
<p>Если Вы не посылали запрос, или кто-то это сделал за Вас, просто проигнорируйте это письмо.</p>
<p>Спасибо!</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Ваша подписка подтверждена! Спасибо, {name}!</p>";

$options['confirmed_subject'] =
"Добро пожаловать, {name}";

$options['confirmed_message'] =
"<p>Вы были успешно подписаны на новостную ленту {blog_title}.</p>
<p>Спасибо!</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Пожалуйста, подведите свой отказ от подписки, кликнув <a href=\"{unsubscription_confirm_url}\">здесь</a>.</p>";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>Это сделает нам немножечко больно, но мы отписали Вас от получения новостей...</p>";

$options['unsubscribed_subject'] =
"До свидания, {name}";

