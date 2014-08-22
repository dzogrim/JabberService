Prosody account manager for La Quadrature du Net
================================================

This is the code used by https://jabber.lqdn.fr/ to manage prosody accounts 
on our jabber server.

It is distributed under the GPLv3+ License.

Requirements: php5.3, mcrypt, mysql, a small database with the table on dump.sql

a running prosody with the mod_telnet_admin enabled on port 5582.

edit config.sample.php and rename it to config.php

inject the dump.sql into a mysql database 

add the cron.php script to the daily crontab

