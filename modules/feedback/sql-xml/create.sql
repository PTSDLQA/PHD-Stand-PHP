DROP TABLE IF EXISTS `{section}_arch`;
CREATE TABLE `{section}_arch` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `iduser` int(11) NOT NULL default '0',
  `message` text,
  `data` blob,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`),
  KEY `iduser` (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;