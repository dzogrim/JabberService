<?php

mysql_connect("localhost","user","password");
mysql_select_db("database");

$csrf_key="random long string of characters (seriously, change me, pwgen 40 1 is good ;) )";

$domain="jabber.lqdn.fr";

require_once("functions.php"); 

