<?php

mysql_connect("localhost","user","password");
mysql_select_db("database");

$csrf_key="random long string of characters (seriously, change me, pwgen 40 1 is good ;) )";

$domain="jabber.lqdn.fr";

// After this many days without login, an account is disabled (random password in prosody, disabled in the database)
$disable_timeout=190;
// After this many days withoug login, an account is destroyed for good both in prosody and in the db
$destroy_timeout=370;
// When you create an account, you must log into it before that many days, after that it is destroyed
$firstlogin_timeout=7;

require_once("functions.php"); 

