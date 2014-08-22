<?php
/*
    Prosody Account Manager
    Copyright (C) 2014 Benjamin Sonntag <benjamin@sonntag.fr>, SKhaen <skhaen@cyphercat.eu>   

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    You can find the source code of this software at https://github.com/LaQuadratureDuNet/JabberService
 */

require_once("config.php"); 

$debug=false;
$fields=array("login","pass1","pass2","email","url","cap");
$found=0;
foreach($fields as $f) if (isset($_POST[$f])) $found++;
$error=array();
$info=array();

if ($found==6 && $_POST["url"]=="") {
  if ($_SESSION["captcha"]!=$_POST["cap"]) {
    $error[]=_("The captcha is incorrect, please try again"); 
  }
  if (!csrf_check($_POST["csrf"])) {
    $error[]=_("The captcha is incorrect, please try again (2)"); 
  }
  $_POST["login"]=mb_strtolower($_POST["login"], 'UTF-8');
  $login=fixlogin($_POST["login"]);
  if ($login!=$_POST["login"] || strlen($login)<3 || strlen($login)>80) {
    $error[]=_("The login must be between 3 and 80 characters long, and must not contains special characters (unicode and accents authorized though)");
  }
  $pass=fixlogin($_POST["pass1"]);
  if ($_POST["pass1"]!=$_POST["pass2"] || $pass!=$_POST["pass1"]) {
    $error[]=_("Your passwords are not the same, or contains special characters (unicode and accents authorized though), please try again");
  }
  if (count($error)==0) {
    sleep(5); // Let create some artificial waiting for the one who want to create many accounts ...
    // Try to create the account.
    $already=@mysql_fetch_assoc(mysql_query("SELECT id FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@".$domain)."';"));
    if ($already) {
      $error[]=_("This account already exist, or is disabled. You can't create that login now, please find another one!");
    }

    if (count($error)==0) {

      mysql_query("INSERT INTO accounts SET jabberid='".addslashes($_POST["login"]."@".$domain)."', createdate=NOW(), email='".hashmail(trim($_POST["email"]))."', ack=0;");
      // Connect to the telnet console of prosody.
      $f=fsockopen("localhost",5582,$errno,$errstr,5);
      if (!$f) {
	$error[]=_("Can't connect to jabber server");
	mysql_query("DELETE FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@".$domain)."';");
      } else {
	for($i=0;$i<$pass_line_count_telnet;$i++) {
	  $s=fgets($f,1024);
	  if ($debug) echo ":".$s.":<br>";
	}
	fputs($f,'user:create("'.$login.'@'.$domain.'","'.$pass."\")\n");
	$s=fgets($f,1024);
	if ($debug) echo ":".$s.":<br>";
	if (trim($s)=="| OK: User created") {
	  mysql_query("UPDATE accounts SET ack=1 WHERE jabberid='".addslashes($_POST["login"]."@".$domain)."';");
	  $info[]=_("Your account has been created successfully. You can use it immediately.");
	  unset($_POST);
	  unset($_REQUEST);
	  require_once("nothing.php");
	  exit();
	} else {
	  if ($debug) { $s=fgets($f,1024); echo ":".$s.":<br>"; }
	  mysql_query("DELETE FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@".$domain)."';");
	  $error[]=_("An error occurred trying to create your account, please try again later");
	  // TODO : send an email to us ;) 
	}
	fclose($f);
      }
    }
  }
}

