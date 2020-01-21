-- CREATE DATABASE logs6;
-- GRANT ALL PRIVILEGES ON logs6.* TO 'logs6'@'localhost' IDENTIFIED BY 'PASSWORD';
-- FLUSH PRIVILEGES;
-- quit;

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `start` timestamp NOT NULL DEFAULT current_timestamp(),
  `stop` timestamp NULL DEFAULT NULL,
  `stopcause` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT 'normal',
  `user` varchar(200) COLLATE utf8_bin NOT NULL,
  `mac` varchar(17) COLLATE utf8_bin NOT NULL,
  `nas` varchar(20) COLLATE utf8_bin NOT NULL,
  `service` varchar(100) COLLATE utf8_bin NOT NULL,
  `ipv4` varchar(15) COLLATE utf8_bin NOT NULL,
  `remoteipv6` varchar(46) COLLATE utf8_bin DEFAULT NULL,
  `dhcpv6pd` varchar(46) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;