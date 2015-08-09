<?php

if(!session_id()) session_start();

//$opcoes = get_option('Anderson_Makiyama_Captcha_On_Login_options');

$text_color = isset($_SESSION['Anderson_Makiyama_Captcha_On_Login_font_color'])?$_SESSION['Anderson_Makiyama_Captcha_On_Login_font_color']:'0x00f00000';
$background = isset($_SESSION['Anderson_Makiyama_Captcha_On_Login_background'])?$_SESSION['Anderson_Makiyama_Captcha_On_Login_background']:0;

if($background == 0) $background = mt_rand(1,8);

$code = isset($_SESSION['Anderson_Makiyama_Captcha_On_Login_code'])?$_SESSION['Anderson_Makiyama_Captcha_On_Login_code']:'';

$image = imagecreatefromjpeg("images/".$background.".jpg");

$width = 160;
$height = 60;

$font = 'fonts/chp-fire.ttf';

$font_size = $height * 0.60;

//$text_color = imagecolorallocate($image, 20, 40, 100);

$noise_color = imagecolorallocate($image, 100, 120, 180);

$textbox = imagettfbbox($font_size, 0, $font, $code) or die('Erro na funчуo imagettfbbox');

$x = ($width - $textbox[4])/2;

$y = ($height - $textbox[5])/2; $y = $y -12;

imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Erro na funчуo imagettftext');

header('Content-Type: image/jpeg');

imagejpeg($image);

imagedestroy($image);

?>