ALTER TABLE `{section}_catalog` ADD `svote` int(11) NOT NULL default 0;
ALTER TABLE `{section}_catalog` ADD `cvote` int(11) NOT NULL default 0;

INSERT INTO `{domain}_options` (`item`,`idgroup`,`name`,`var`,`value`,`type`) VALUES ('{section}',3,'Использовать оценки','usevote','0','bool');