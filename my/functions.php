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


// We have to pass this number of lines when telneting to the prosody admin console:
$pass_line_count_telnet=12;
// automatic session starting (for csrf/captcha management)
session_start();

require_once("lang.php");

function __($str) { echo _($str); }

function ehe($str) { echo htmlentities($str); }
function eher($str) { if (isset($_REQUEST[$str])) ehe($_REQUEST[$str]); }


function csrf_gen() {
  global $csrf_key;
  if (!isset($_SESSION["csrf"])) {
    $_SESSION["csrf"]=rand();
  }
  $i=substr(md5(rand()),0,10);
  return $i.md5($csrf_key."-".$i."-".$_SESSION["csrf"]);
}

function csrf_check($str) {
  global $csrf_key;
  if (!isset($_SESSION["csrf"])) {
    // should not happen, but at least prevent a warning...
    $_SESSION["csrf"]=rand();
  }
  $str=strtolower($str);
  if (!preg_match('#[0-9a-f]{42}#',$str)) {
    return false;
  }
  return ( $str == substr($str,0,10).md5($csrf_key."-".substr($str,0,10)."-".$_SESSION["csrf"]) );
}

function hashmail($mail,$salt="") {
  if ($salt=="") $salt = '$5$rounds=5997$'.substr(md5(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)),0,10).'$';
  return crypt($mail, $salt);
}

function fixlogin($str) {
  // remove forbidden characters: 
  $remove=",'\"\\\n\r ".chr(9);
  for($i=0;$i<strlen($remove);$i++) {
    $str=str_replace(substr($remove,$i,1),"",$str);
  }
  return $str;
}

