#!/usr/bin/php
<?php

   /* Lanch this script as a weekly cron job. 
    * Since it hammers the MySQL table quite badly, don't launch it too often on a busy server ;) 
    */
require_once("my/config.php");
$debug=false;

// TODO: check with accounts having - or _ since urlencode doesn't encode those either !

function myurlencode($str) {
  return str_replace(".","%2e",urlencode($str));
}
function myurldecode($str) {
  return urldecode(str_replace("%2e",".",$str));
}

$root="/var/lib/prosody/".myurlencode($domain)."/lastlog";
$d=opendir($root);
while ($c=readdir($d)) {
  if (preg_match("#^(.*)\.dat$#",$c,$mat)) {
    $user=$mat[1];
    $f=@fopen($root."/".$c,"rb");
    if ($f) {
      while ($s=fgets($f,1024)) {
	if (preg_match('#\["timestamp"\] = ([0-9]*);#',$s,$mat)) {
	  mysql_query("UPDATE accounts SET lastlogin=FROM_UNIXTIME('".$mat[1]."') WHERE jabberid='".addslashes(myurldecode($user)."@".$domain)."';");
	  echo urldecode($user)." lastlogin ".date("Y-m-d H:i:s",$mat[1])."\n";
	}
      }
      fclose($f);
    }
  }
}


$r=mysql_query("SELECT * FROM accounts WHERE lastlogin<DATE_SUB(NOW(), INTERVAL ".$disable_timeout." DAY) AND createdate<DATE_SUB(NOW(), INTERVAL ".$firstlogin_timeout." DAY) AND disabledate IS NULL AND ack=1;");
echo mysql_error();
$isconnected=false;
$timeout=12; // 2 minutes total timeout, enough right?

while ($c=mysql_fetch_array($r)) {
  while (!$isconnected) { 
    $f=fsockopen("localhost",5582,$errno,$errstr,5);
    if (!$f) {
      echo "Can't connect to the admin console, will wait for 10 seconds...\n";
      sleep(5);
      $timeout--;
      if ($timeout==0) {
	echo "Can't connect for good, exiting\n";
	break;
      }
    } else {
      for($i=0;$i<$pass_line_count_telnet;$i++) {
	$s=fgets($f,1024);
	if ($debug) echo ":".$s.":<br>";
      }
      $isconnected=true;
    }
  }
  
  $random=md5(rand().rand().rand());
  echo "Changing password for user ".$c["jabberid"]."\n";  
  $cmd='user:password("'.$c["jabberid"].'","'.$random."\")\n";
  if ($debug) echo "launching command:$cmd\n";
  fputs($f,$cmd);
  $s=fgets($f,1024);
  if ($debug) echo ":".$s.":<br>";
  if (trim($s)=="| OK: User password changed") {
      mysql_query("UPDATE accounts SET disabledate=NOW() WHERE jabberid='".addslashes($c["jabberid"])."';");
      echo mysql_error();
      echo "Disabled account ".$c["jabberid"]."\n";
    } else {
      if ($debug) { $s=fgets($f,1024); echo ":".$s.":<br>"; }
      echo "Can't disable account ".$c["jabberid"]." message was $s\n";
      // TODO : send an email to us ;) 
  }
} // for each account we should disable
  
if ($isconnected) {
  fclose($f);
}


$r=mysql_query("SELECT * FROM accounts WHERE lastlogin<DATE_SUB(NOW(), INTERVAL ".$destroy_timeout." DAY) AND createdate<DATE_SUB(NOW(), INTERVAL ".$firstlogin_timeout." DAY) AND disabledate IS NOT NULL AND ack=1;");
echo mysql_error();
$isconnected=false;
$timeout=12; // 2 minutes total timeout, enough right?

while ($c=mysql_fetch_array($r)) {
  while (!$isconnected) { 
    $f=fsockopen("localhost",5582,$errno,$errstr,5);
    if (!$f) {
      echo "Can't connect to the admin console, will wait for 10 seconds...\n";
      sleep(5);
      $timeout--;
      if ($timeout==0) {
	echo "Can't connect for good, exiting\n";
	break;
      }
    } else {
      for($i=0;$i<$pass_line_count_telnet;$i++) {
	$s=fgets($f,1024);
	if ($debug) echo ":".$s.":<br>";
      }
      $isconnected=true;
    }
  }
  
  echo "Deleting user ".$c["jabberid"]."\n";  
  fputs($f,'user:delete("'.$c["jabberid"].'"'.")\n");
  $s=fgets($f,1024);
  if ($debug) echo ":".$s.":<br>";
  if (trim($s)=="| OK: User deleted") {
      mysql_query("DELETE FROM accounts WHERE jabberid='".addslashes($c["jabberid"])."';");
      echo "Destroyed account ".$c["jabberid"]."\n";
    } else {
      if ($debug) { $s=fgets($f,1024); echo ":".$s.":<br>"; }
      echo "Can't destroy account ".$c["jabberid"]." message was $s\n";
      // TODO : send an email to us ;) 
  }
} // for each account we should disable
  
if ($isconnected) {
  fclose($f);
}


