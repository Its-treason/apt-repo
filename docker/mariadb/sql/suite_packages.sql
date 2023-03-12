CREATE TABLE `suite_packages` (
  `codename` varchar(64) NOT NULL,
  `suite` varchar(64) NOT NULL,
  `package_id` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `suite_packages`
  ADD PRIMARY KEY (`codename`, `suite`, `package_id`);
COMMIT;
