DROP TABLE IF EXISTS `{section}_variants`;
CREATE TABLE `{section}_variants` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `sort` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{section}_arch`;
CREATE TABLE `{section}_arch` (
  `id` int(11) NOT NULL auto_increment,
  `date1` int(11) NOT NULL default '0',
  `date2` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `result` blob,
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;