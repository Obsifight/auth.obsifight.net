DROP TABLE IF EXISTS `mac_addresses`;

CREATE TABLE `mac_addresses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `session_cache`;

CREATE TABLE `session_cache` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `server_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `mac_addresses_banned`;

CREATE TABLE `mac_addresses_banned` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(50) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;