DROP TABLE IF EXISTS `{section}`;
CREATE TABLE `{section}` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `idsec` int(11) NOT NULL default '0',
  `iditem` int(11) NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  `idtags` varchar(150) default NULL,
  `stems` text default NULL,
  `content` text default NULL,
  PRIMARY KEY  (`id`),
  KEY `idsec` (`idsec`),
  KEY `iditem` (`iditem`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `stems` (`stems`),
  FULLTEXT KEY `content` (`content`),
  FULLTEXT KEY `idtags` (`idtags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{section}_tags`;
CREATE TABLE `{section}_tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(50) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `tag` (`tag`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;