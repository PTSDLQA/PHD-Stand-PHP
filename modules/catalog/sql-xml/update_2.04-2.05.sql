ALTER TABLE `{section}_categories` CHANGE `latname` `urlname` varchar(100) NOT NULL default '';
ALTER TABLE `{section}_catalog` CHANGE `latname` `urlname` varchar(100) NOT NULL default '';
INSERT INTO `{domain}_options` (`item`,`idgroup`,`name`,`var`,`value`,`type`) VALUES ('{section}',4,'Длина автоаннотации','anonslen','350','int');
INSERT INTO `{domain}_options` (`item`,`idgroup`,`name`,`var`,`value`,`type`) VALUES ('{section}',4,'Использовать дату публикации','usedate','1','bool');