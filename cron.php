<?php

// regenerate header/footer every day
//if (filemtime("header.php")+86400 < time()) {
  $s=file_get_contents("https://jabber.lqdn.fr");
  $lines=explode("\n",$s);
  $header=fopen("header.php.temp","wb");
  $footer=fopen("footer.php.temp","wb");
  $pos=0;
  foreach($lines as $line) {
    if (preg_match("#<article #",$line)) {
      $pos=1;
    }
    if (preg_match("#</article#",$line)) {
      $pos=2;
    } 
    if ($pos==0) fputs($header,$line);
    if ($pos==2) fputs($footer,$line);
  }
  fclose($header);
  fclose($footer);
  rename("header.php.temp","header.php");
  rename("footer.php.temp","footer.php");
//}



