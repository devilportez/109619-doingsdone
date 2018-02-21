CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE projects (
	id int NOT NULL AUTO_INCREMENT,
	name char NOT NULL UNIQUE,
	user_id int NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE users (
	id int NOT NULL AUTO_INCREMENT,
	signup_date DATETIME NOT NULL,
	email char NOT NULL UNIQUE,
	name char NOT NULL,
	password char NOT NULL,
	contacts char,
	PRIMARY KEY (id)
);

CREATE TABLE tasks (
	id int NOT NULL AUTO_INCREMENT,
	create_date DATETIME,
	done_date DATETIME,
	name char NOT NULL,
	file char,
	deadline DATETIME,
	user_id int NOT NULL,
	project_id int NOT NULL,
	PRIMARY KEY (id)
);

ALTER TABLE projects
  ADD CONSTRAINT projects_fk0
  FOREIGN KEY (user_id)
  REFERENCES users(id);

ALTER TABLE tasks
  ADD CONSTRAINT tasks_fk0
  FOREIGN KEY (user_id)
  REFERENCES users(id);

ALTER TABLE tasks
  ADD CONSTRAINT tasks_fk1
  FOREIGN KEY (project_id)
  REFERENCES projects(id);

CREATE INDEX deadline ON tasks(deadline)
