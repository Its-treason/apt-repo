CREATE TABLE `github_subscription` (
  `owner` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `last_release` varchar(64)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `github_subscription`
  ADD PRIMARY KEY (`owner`, `name`);
COMMIT;
