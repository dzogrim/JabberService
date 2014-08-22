Prosody account manager for La Quadrature du Net
================================================

This is the code and configuration used by [La Quadrature du Net](https://www.laquadrature.net) for its public Jabber/Xmpp chat service at https://jabber.lqdn.fr/

It is distributed under the [aGPLv3+](COPYRIGHT) License.

Web control panel
-------------------

To install the web control panel, proceed as follow (on a debian install)

    aptitude install apache2-mpm-prefork libapache2-mod-php5 php5-mysql mysql-server php5-mcrypt prosody pwgen
    
* Create an account and a database on the mysql server, insert the dump.sql file into that database
* Edit the configuration file my/config.sample.php and rename it to my/config.php
* configure [Prosody](https://prosody.im/) with the module [mod_admin_telnet](https://prosody.im/doc/modules/mod_admin_telnet) enabled on port 5582.
* add the cron.php script to the weekly crontab
* point an Apache alias on /my to the /my folder

The header.php and footer.php files are generated automatically in our case. Change them according to your needs to get a nice webpage instead of our own blog's template ;) 

Prosody configuration
---------------------

This git repository contains our prosody configuration file. Use it and tweak it to your own needs

Please note that we used some modules from Prosody-modules, which need to be copied from their mercurial repository to /usr/lib/prosody/modules/ before running with our configuration file


Apache2 configuration
---------------------

This git repository contains our apache configuration files in apache2/ Use them and tweak them to your own needs

Iptables configuration
----------------------

This git repository also contains our iptables / Linux Firewall configuration. tweak them to your own needs


Software used in this project
-----------------------------

We would like to thank the free software we use in this public service:

* [Debian](https://www.debian.org/)
* [Prosody](https://prosody.im/)
* [Cool Captcha](https://code.google.com/p/cool-php-captcha/)
* [PhpMailer](https://github.com/PHPMailer/PHPMailer)
* [Prosody-Modules](https://code.google.com/p/prosody-modules/)
* and of course all the usual software to run the Internet fluently ;) (Apache, MySQL, Php, Wordpress, cur, grep, sed, awk, bash, linux, und so weiter...)


TODO
----

test the following modules :

* https://code.google.com/p/prosody-modules/wiki/mod_log_auth + fail2ban (to prevent multiple failed authorizations requests from a single IP address) (warning, in that case only keep 1 day of prosody log!)
* https://code.google.com/p/prosody-modules/wiki/mod_limits to limit the bandwidth allocated to each IP address.

