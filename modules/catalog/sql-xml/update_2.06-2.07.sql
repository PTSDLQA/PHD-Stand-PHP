ALTER TABLE `{section}_catalog` ADD `mdate` int(11) NOT NULL default 0;
UPDATE `{section}_catalog` SET `mdate`=`date`;