ALTER TABLE `{section}_categories` ADD `active` enum('Y','N') NOT NULL default 'Y';
ALTER TABLE `{section}_categories` ADD INDEX (`active`);