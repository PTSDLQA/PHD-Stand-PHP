DROP TABLE IF EXISTS `{section}_categories`;
CREATE TABLE `{section}_categories` (
  `id` int(11) NOT NULL auto_increment,
  `idker` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `idimg` int(11) NOT NULL default '0',
  `name` varchar(150) NOT NULL default '',
  `urlname` varchar(100) NOT NULL default '',
  `description` text NOT NULL default '',
  `tags` text NOT NULL default '',
  `citems` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `active` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`id`),
  KEY `idker` (`idker`),
  KEY `level` (`level`),
  KEY `name` (`name`),
  KEY `urlname` (`urlname`),
  KEY `sort` (`sort`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{section}_albums`;
CREATE TABLE `{section}_albums` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `idcat` int(11) NOT NULL default '0',
  `idimg` int(11) NOT NULL default '0',
  `name` varchar(150) NOT NULL default '',
  `urlname` varchar(100) NOT NULL default '',
  `description` text NOT NULL default '',
  `tags` text NOT NULL default '',
  `comments` int(11) NOT NULL default '0',
  `cvote` int(11) NOT NULL default '0',
  `svote` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `active` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`id`),
  KEY `date` (`date`),
  KEY `idcat` (`idcat`),
  KEY `name` (`name`),
  KEY `urlname` (`urlname`),
  KEY `sort` (`sort`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
