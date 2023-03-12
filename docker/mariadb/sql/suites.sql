CREATE TABLE `suites` (
  `codename` varchar(64) NOT NULL,
  `suite` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `suites`
  ADD PRIMARY KEY (`codename`, `suite`);
COMMIT;
