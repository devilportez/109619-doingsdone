USE `109619-doingsdone`;

-- Добавление пользователей
INSERT INTO `users` SET
  `register_date` = '2018-01-31',
  `email` = 'ignat.v@gmail.com',
  `name` = 'Игнат',
  `password` = '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka';
INSERT INTO `users` SET
  `register_date` = '2018-01-31',
  `email` = 'kitty_93@li.ru',
  `name` = 'Леночка',
  `password` = '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa';
INSERT INTO `users` SET
  `register_date` = '2018-01-31',
  `email` = 'warrior07@mail.ru',
  `name` = 'Руслан',
  `password` = '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW';

  -- Добавление имен проектов
INSERT INTO `projects` SET `name` = 'Входящие', `user_id` = 1;
INSERT INTO `projects` SET `name` = 'Учеба', `user_id` = 1;
INSERT INTO `projects` SET `name` = 'Работа', `user_id` = 1;
INSERT INTO `projects` SET `name` = 'Домашние дела', `user_id` = 1;
INSERT INTO `projects` SET `name` = 'Авто', `user_id` = 1;

-- Добавление задач
INSERT INTO `tasks` SET
  `name` = 'Собеседование в IT компании',
  `deadline` = '2018-06-01',
  `user_id` = 1,
  `project_id` = 4;
INSERT INTO `tasks` SET
  `name` = 'Выполнить тестовое задание',
  `deadline` = '2018-05-25',
  `user_id` = 1,
  `project_id` = 4;
INSERT INTO `tasks` SET
  `name` = 'Сделать задание первого раздела',
  `deadline` = '2018-04-21',
  `user_id` = 1,
  `project_id` = 3;
INSERT INTO `tasks` SET
  `name` = 'Встреча с другом',
  `deadline` = '2018-04-22',
  `user_id` = 1,
  `project_id` = 2;
INSERT INTO `tasks` SET
  `name` = 'Купить корм для кота',
  `deadline` = '2018-02-08',
  `user_id` = 1,
  `project_id` = 5;
INSERT INTO `tasks` SET
  `name` = 'Заказать пиццу',
  `deadline` = '2018-02-09',
  `user_id` = 1,
  `project_id` = 5;

-- Получение списка из всех проектов для одного пользователя
SELECT `name` FROM `projects` WHERE `id` = 1;

-- Получение списка из всех задач для одного проекта
SELECT * FROM `tasks` WHERE `project_id` = 4;

-- Помечание задачи как выполненная
UPDATE `tasks` SET `done_date` = '2018-02-10' WHERE `id` = 1;

-- Получение всех задач для завтрашнего дня
SELECT * FROM `tasks` WHERE `deadline` = '2018-02-22';

-- Обновление названия задачи по её идентификатору
UPDATE `tasks` SET `name` = 'Варить борщи' WHERE `id` = 1;
