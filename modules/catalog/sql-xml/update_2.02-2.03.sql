UPDATE `{domain}_options` SET `superonly`='Y' WHERE `item`='{section}' AND `var`='mysort';
ALTER TABLE `all_ru_categories` ADD INDEX (`weight`);