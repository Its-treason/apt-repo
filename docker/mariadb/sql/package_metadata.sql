CREATE TABLE `package_metadata` (
  `package_id` char(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `arch` varchar(25) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `fullinfo` blob NOT NULL,
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `package_metadata`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `name` (`name`),
  ADD KEY `filename` (`filename`),
  ADD KEY `upload_date` (`upload_date`);
COMMIT;
