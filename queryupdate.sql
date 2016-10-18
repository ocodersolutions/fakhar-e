ALTER TABLE `logs` ADD `filterTypeMain` VARCHAR(50) NOT NULL AFTER `profileBasePrices`, ADD `orderBy` VARCHAR(50) NOT NULL AFTER `filterTypeMain`, ADD `searchArticle` VARCHAR(500) NOT NULL AFTER `orderBy`, ADD `searchVenue` VARCHAR(500) NOT NULL AFTER `searchArticle`;

CREATE TABLE `articlealert` (
  `alertid` int(50) NOT NULL,
  `feeddataid` int(50) NOT NULL,
  `userId` int(50) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `articlealert`
  ADD PRIMARY KEY (`alertid`);
ALTER TABLE `articlealert`
  MODIFY `alertid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;
  ALTER TABLE `logs` ADD `searchVenue` VARCHAR(500) NOT NULL AFTER `searchArticle`;


/*=== Update by Trung ===*/
/* CREATE AttributeList View */
select `p2`.`title` AS `attribute_name`,`p3`.`title` AS `attribute_value` from ((`ProductReferenceAttributes` `p1` join `ProductReferenceAttributes` `p2` on((`p2`.`attributeParentId` = `p1`.`attributeId`))) join `ProductReferenceAttributes` `p3` on((`p3`.`attributeParentId` = `p2`.`attributeId`))) where (`p1`.`attributeParentId` = 2) group by `p2`.`title`,`p3`.`title`

CREATE TABLE IF NOT EXISTS `Venue` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `parentId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `Venue`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `Venue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `elnove.local`.`Venue` (`id`, `title`, `parentId`, `userId`, `created`, `updated`, `isActive`) VALUES (NULL, 'Work', '0', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1'), (NULL, 'Night Out', '0', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1'), (NULL, 'Casual', '0', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');

CREATE TABLE `venuestyle` (
  `id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `venuestyle`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `venuestyle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;


  INSERT INTO `venuestyle` (`id`, `style_id`, `venue_id`) VALUES
(59, 29, 13),
(61, 30, 5),
(62, 33, 11),
(63, 29, 2),
(64, 33, 8),
(65, 33, 2);



CREATE TABLE IF NOT EXISTS `Style` (
  `id` int(11) NOT NULL,
  `venueId` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `isActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `Style`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `Style`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE IF NOT EXISTS `StyleDefination` (
  `id` int(11) NOT NULL,
  `styleId` int(11) NOT NULL,
  `attribute` varchar(500) NOT NULL,
  `value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `StyleDefination`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `StyleDefination`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;