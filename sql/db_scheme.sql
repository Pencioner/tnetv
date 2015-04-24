CREATE TABLE IF NOT EXISTS `event_manager_observers` (
  `o_id`                   INTEGER NOT NULL AUTO_INCREMENT,
  `o_singleton_classname`  VARCHAR(256) DEFAULT NULL,
  `o_methodname`           VARCHAR(256) NOT NULL,
  `o_eventname`            VARCHAR(256) NOT NULL,
  PRIMARY KEY (`o_id`)
);

CREATE TABLE IF NOT EXISTS `comments` (
  `c_id`        INTEGER NOT NULL AUTO_INCREMENT,
  `c_name`      VARCHAR(256) NOT NULL,
  `c_comment`   TEXT NOT NULL,
  `c_added_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`c_id`),
  KEY `c_name` (`c_name`)
);


