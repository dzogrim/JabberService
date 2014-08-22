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
$fields=array("csrf","id","key","pass1","pass2","url");
$found=0;
foreach($fields as $f) if (isset($_POST[$f])) $found++;
$error=array();
$info=array();

if ($found==6 && $_POST["url"]=="") {
  $_GET["id"]=$_POST["id"];
  $_GET["key"]=$_POST["key"];

  if ($_SESSION["captcha"]!=$_POST["cap"]) {
    $error[]=_("The captcha is incorrect, please try again"); 
  }
  if (!csrf_check($_POST["csrf"])) {
    $error[]=_("The captcha is incorrect, please try again (2)"); 
  }
  $id=intval($_POST["id"]);
  if (!$id || !preg_match('#^[0-9a-f]{16}$#',$_POST["key"])) {
    $error[]=_("The url is incorrect. please check your mail or contact us."); 
  }
  if (count($error)==0) {
    // Does it exist? 
    $already=@mysql_fetch_assoc(mysql_query("SELECT * FROM accounts WHERE id='".$id."';"));
    if (!$already) {
      $error[]=sprintf(_("This account doesn't exist, or have been permanently destroyed. <a href=\"%s\">Click here to create a new account with this login</a>."),"create.php");
    }
    $key=substr(md5($csrf_key."-".$already["id"]."-".$already["jabberid"]),0,16);
    if ($key!=$_POST["key"]) {
      $error[]=_("The provided key is incorrect, please check your mail or contact us.");
    }
    $pass=fixlogin($_POST["pass1"]);
    if ($_POST["pass1"]!=$_POST["pass2"] || $pass!=$_POST["pass1"]) {
      $error[]=_("Your passwords are not the same, or contains special characters (unicode and accents authorized though), please try again");
    }
    if ($already["disabledate"]!="") {
      $error[]=sprintf(_("This account have been disabled. <a href=\"%s\">Click here to restore it</a>."),"recover.php");
    }
    if (count($error)==0) {
      // change the password for good (form)
      
      // Connect to the telnet console of prosody.
      $f=fsockopen("localhost",5582,$errno,$errstr,5);
      if (!$f) {
	$error[]=_("Can't connect to jabber server");
      } else {
	for($i=0;$i<$pass_line_count_telnet;$i++) {
	  $s=fgets($f,1024);
	  if ($debug) echo ":".$s.":<br>";
	}
	fputs($f,'user:password("'.$already["jabberid"].'","'.$pass."\")\n");
	$s=fgets($f,1024);
	if ($debug) echo ":".$s.":<br>";
	if (trim($s)=="| OK: User password changed") {
	  $info[]=sprintf(_("Your password for account %s has been changed, you can use your new password right now."),$already["jabberid"]);
	  unset($_POST);
	  unset($_REQUEST);
	  require_once("nothing.php");
	  exit();
	} else {
	  if ($debug) { $s=fgets($f,1024); echo ":".$s.":<br>"; }
	  $error[]=_("An error occurred trying to change your password, please try again later");
	  // TODO : send an email to us ;) 
	}
	fclose($f);
      }
    } // still no error ? 
  } // no error ?
} // isset ?

