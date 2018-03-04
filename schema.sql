CREATE DATABASE `109619-doingsdone`
  DEFAULT CHARACTER SET `utf8`
  DEFAULT COLLATE `utf8_general_ci`;

USE `109619-doingsdone`;

CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `register_date` DATETIME NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contacts` varchar(255),
  PRIMARY KEY (`id`)
);

CREATE TABLE `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `create_date` DATETIME,
  `done_date` DATETIME,
  `name` varchar(255) NOT NULL,
  `file` varchar(255),
  `deadline` DATETIME,
  `user_id` int NOT NULL,
  `project_id` int NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `projects`
  ADD CONSTRAINT `projects_fk0`
  FOREIGN KEY (`user_id`)
  REFERENCES `users`(`id`);

ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_fk0`
  FOREIGN KEY (`user_id`)
  REFERENCES `users`(`id`);

ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_fk1`
  FOREIGN KEY (`project_id`)
  REFERENCES `projects`(`id`);

CREATE INDEX `deadline` ON `tasks`(`deadline`);
