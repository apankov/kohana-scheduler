DROP TABLE IF EXISTS `scheduler_tasks`;
CREATE TABLE `scheduler_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `queue` varchar(100) NOT NULL DEFAULT '',
  `args` mediumtext NOT NULL,
  `status` enum('active','suspended','disabled') NOT NULL DEFAULT 'active',
  `crontab` varchar(100) NOT NULL DEFAULT '',
  `next_scheduled_at` int(11) DEFAULT NULL,
  `last_ran_at` int(11) DEFAULT NULL,
  `last_job_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
