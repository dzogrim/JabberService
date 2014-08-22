<?php
  require_once("captcha.php");
$captcha->wordsFile = 'words/es.php';
$captcha->lineWidth = 1;
$captcha->scale = 6; $captcha->blur = true;
$captcha->resourcesPath = "/var/www/htdocs/my";

if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $langs = array('en', 'es', 'fr', 'de');
    $lang  = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($lang, $langs)) {
        $captcha->wordsFile = "words/$lang.php";
    }
}

// Image generation
$captcha->CreateImage();

?>