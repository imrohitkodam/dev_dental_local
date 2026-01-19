CREATE TABLE IF NOT EXISTS `#__tagreport_tag_time` (
  `tag_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `course_time` int(50) NOT NULL,
  `event_time` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
