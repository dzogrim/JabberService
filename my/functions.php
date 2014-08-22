<?php


function __($str) { echo _($str); }

function ehe($str) { echo htmlentities($str); }
function eher($str) { if (isset($_REQUEST[$str])) ehe($_REQUEST[$str]); }


function csrf_gen() {
  global $csrf_key;
  $i=substr(md5(rand()),0,10);
  return $i.md5($csrf_key."-".$i);
}

function csrf_check($str) {
  global $csrf_key;
  $str=strtolower($str);
  if (!preg_match('#[0-9a-f]{42}#',$str)) {
    return false;
  }
  return ( $str == substr($str,0,10).md5($csrf_key."-".substr($str,0,10)) );
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
