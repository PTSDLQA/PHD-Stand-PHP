ALTER TABLE `{section}` ADD `idfile` int(11) NOT NULL default 0;
ALTER TABLE `{section}` ADD `active` enum('Y','N') NOT NULL default 'Y';
ALTER TABLE `{section}` ADD INDEX (`active`);

INSERT INTO `{domain}_options` (`item`,`idgroup`,`name`,`var`,`value`,`type`)
VALUES ('{section}',1,'Использовать прикрепляемый xls файл','usexls','0','bool');

INSERT INTO `{domain}_options` (`item`,`idgroup`,`name`,`var`,`value`,`type`)
VALUES ('{section}',1,'Шаблон для новых страниц','tpldefault','pages_page.tpl','string');