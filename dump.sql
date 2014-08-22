CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jabberid` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `disabledate` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  `email` varchar(255) NOT NULL,
  `ack` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
