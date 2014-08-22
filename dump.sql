CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jabberid` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `disabledate` datetime DEFAULT NULL,
  `lastlogin` datetime NOT NULL,
  `email` varchar(255) NOT NULL,
  `ack` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `jabberid` (`jabberid`),
  KEY `lastlogin` (`lastlogin`),
  KEY `createdate` (`createdate`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
