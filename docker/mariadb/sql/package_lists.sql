CREATE TABLE `package_lists` (
  `path` varchar(64) NOT NULL,
  `content` blob NOT NULL,
  `size` int(11) NOT NULL,
  `md5sum` char(32) NOT NULL,
  `sha1` char(40) NOT NULL,
  `sha256` char(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `package_lists`
  ADD PRIMARY KEY (`path`);
COMMIT;
