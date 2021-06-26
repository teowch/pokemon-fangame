CREATE SCHEMA `dev_stadium`;
USE `dev_stadium`;

CREATE TABLE `leader` (
  `leader_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `team_id` int NOT NULL,
  `type` varchar(9) NOT NULL,
  PRIMARY KEY (`leader_id`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`)
) ENGINE=InnoDB;

CREATE TABLE `move` (
  `move_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`move_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `pokemon` (
  `pokemon_id` int NOT NULL AUTO_INCREMENT,
  `species_id` int NOT NULL,
  `ivs` varchar(18) NOT NULL,
  `evs` varchar(24) NOT NULL,
  PRIMARY KEY (`pokemon_id`),
  FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`)
) ENGINE=InnoDB;

CREATE TABLE `pokemon_move` (
  `pokemon_id` int NOT NULL,
  `move_id` int NOT NULL,
  FOREIGN KEY (`pokemon_id`) REFERENCES `pokemon` (`pokemon_id`),
  FOREIGN KEY (`move_id`) REFERENCES `move` (`move_id`)
) ENGINE=InnoDB;

CREATE TABLE `species` (
  `species_id` int NOT NULL,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`species_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `team` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB;

CREATE TABLE `team_pokemon` (
  `team_id` int NOT NULL,
  `pokemon_id` int NOT NULL,
  PRIMARY KEY (`team_id`,`pokemon_id`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`),
  FOREIGN KEY (`pokemon_id`) REFERENCES `pokemon` (`pokemon_id`)
) ENGINE=InnoDB;

CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `user_team` (
  `user_id` int NOT NULL,
  `team_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`team_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`)
) ENGINE=InnoDB;
