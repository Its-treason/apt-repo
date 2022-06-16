CREATE TABLE `repository_info` (
  `field` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `repository_info`
  ADD PRIMARY KEY (`field`);
COMMIT;

INSERT INTO `repository_info` (`field`, `value`) VALUES
 ('Date', 'Tue, 14 Jun 2022 18:57:34 UTC'),
 ('Description', 'An example software repository'),
 ('Label', 'Example'),
 ('Origin', 'Example Repository'),
 ('Version', '1.0');
