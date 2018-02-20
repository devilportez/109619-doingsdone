CREATE DATABASE doingsdone;
DEFAULT CHARACTER SET utf8;
DEFAULT COLLATE utf8_general_ci;
USE doingsdone;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  signup_date DATETIME,
  email CHAR,
  name CHAR,
  password CHAR,
  contacts TEXT
);

CREATE TABLE projects (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name CHAR
);

CREATE TABLE tasks (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name CHAR,
  create_date DATETIME,
  done_date DATETIME,
  file CHAR,
  term DATETIME
);
