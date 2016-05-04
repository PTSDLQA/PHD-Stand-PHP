DROP TABLE IF EXISTS `{section}`;
CREATE TABLE `{section}` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `idker` int(11) NOT NULL default '0',
  `level` smallint(6) NOT NULL default '0',
  `urlname` varchar(100) NOT NULL default '',
  `name` varchar(150) NOT NULL default '',
  `content` longtext default NULL,
  `keywords` text default NULL,
  `description` text default NULL,
  `tags` text default NULL,
  `template` varchar(50) NOT NULL default 'pages_page.tpl',
  `sort` int(11) NOT NULL default '0',
  `type` enum('page','dir') NOT NULL default 'page',
  `active` enum('Y','N') NOT NULL default 'Y',
  `inmap` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`id`),
  KEY `idkername` (`idker`,`type`,`urlname`),
  KEY `urlname` (`urlname`),
  KEY `level` (`level`),
  KEY `sort` (`sort`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;