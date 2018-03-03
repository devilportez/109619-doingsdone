<?php
/**
 * Функция-шаблонизатор
 * @param string $template Путь к шаблону
 * @param array $data Массив с данными
 * @return string Шаблон с переданными данными либо пустая строка, если файл
 * шаблона не найден
 */
function set_template ($template, $data) {
    if (file_exists($template)) {
        extract($data);
        ob_start();
        require_once($template);
        return ob_get_clean();
    }
    return "";
}

/**
 * Формирует sql запрос на добавление проекта
 * @param mysqli $db_connect Ресурс подключения
 * @param integer $user_id ID пользователя
 * @param string $project_name Имя проекта
 */
function add_project ($db_connect, $user_id, $project_name) {
    $sql_query = "INSERT INTO `projects` SET `user_id` = ?, `name` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "is", $user_id, $project_name);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    header("Location: /");
}

/**
 * Формирует sql запрос на добавление задачи
 * @param mysqli $db_connect Ресурс подключения
 * @param string $task_name Текст задачи
 * @param string $file Путь к файлу, если он был загружен
 * @param string $deadline Срок выполнения задачи
 * @param integer $user_id ID пользователя
 * @param integer $project_id ID проекта
 */
function add_task ($db_connect, $task_name, $file, $deadline, $user_id, $project_id) {
    $sql_query = "INSERT INTO `tasks` SET `create_date` = NOW(), `name` = ?, `file` = ?, `deadline` = ?, `user_id` = ?, `project_id` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "sssii", $task_name, $file, $deadline, $user_id, $project_id);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    header("Location: /");
}

/**
 * Получает количество тасков в проекте
 * @param array $tasks Массив с задачами
 * @param integer $project_id ID проекта
 * @return integer Число задач
 */
function get_tasks_amount ($tasks, $project_id) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project_id === 0) {
            $count = count($tasks);
        }
        if ($task["project_id"] === $project_id) {
            $count++;
        }
    }
    return $count;
}

/**
 * Получает срочные задачи со сроком исполнения меньше дня
 * @param string $date Дата задачи
 * @return boolean True, если до срока исполнения меньше дня
 */
function get_urgent_task ($date) {
    date_default_timezone_set("Europe/Moscow");
    $current_timestamp = time();
    $task_timestamp = strtotime($date);
    $seconds_in_day = 86400;
    $difference = floor(($task_timestamp - $current_timestamp) / $seconds_in_day);
    if ((int) $difference < 1) {
        return true;
    }
    return false;
}

/**
 * Фильтрует задачи по принадлежности к проекту
 * @param array $tasks Массив с задачами
 * @param integer $project_id ID проекта
 * @param integer $show_complete_tasks Показывать ли выполненные задачи
 * @return array Массив с задачами для переданного проекта
 */
function filter_tasks_by_project ($tasks, $project_id, $show_complete_tasks) {
    $filtered_tasks = [];
    if ($show_complete_tasks && $project_id === 0) {
        $filtered_tasks = $tasks;
    }
    foreach ($tasks as $key => $task) {
        if ($show_complete_tasks) {
            if ($project_id === $task["project_id"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        } else {
            if ($project_id === 0 && !$task["done_date"]) {
                $filtered_tasks[] = $tasks[$key];
            }
            if ($project_id === $task["project_id"] && !$task["done_date"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        }
    }
    return $filtered_tasks;
}

/**
 * Фильтрует задачи для показа по определенным срокам исполнения
 * @param array $tasks Массив с задачами
 * @param string $filter Название выбранного фильтра
 * @return array Массив с задачами для выбранного фильтра
 */
function filter_tasks_by_deadline ($tasks, $filter) {
    date_default_timezone_set("Europe/Moscow");
    $current_timestamp = time();
    $seconds_in_day = 86400;
    $filtered_tasks = [];
    foreach ($tasks as $key => $task) {
        $task_timestamp = strtotime($task["deadline"]);
        $difference = floor(($task_timestamp - $current_timestamp) / $seconds_in_day);
        if ($filter === "all") {
            $filtered_tasks = $tasks;
        } elseif ($filter === "today" && (int) $difference === -1) {
            $filtered_tasks[] = $tasks[$key];
        } elseif ($filter === "tomorrow" && (int) $difference === 0) {
            $filtered_tasks[] = $tasks[$key];
        } elseif ($filter === "overdue" && (int) $difference < -1) {
            $filtered_tasks[] = $tasks[$key];
        }
    }
    return $filtered_tasks;
}

/**
 * Загружает файл в директорию
 * @param array $file Массив с данными о загружаемом файле
 * @return string Путь к файлу
 */
function upload_file ($file) {
    if (isset($file["name"]) && file_exists("/uploads")) {
        $file_name = $file["name"];
        $file_path = __DIR__ . "/uploads/";
        $file_url = "/uploads/" . $file_name;
        move_uploaded_file($file["tmp_name"], $file_path . $file_name);
        return $file_url;
    }
    return "";
}

/**
 * Ищет пользователя по переданному емеилу
 * @param mysqli $db_connect Ресурс подключения
 * @param string $email Емеил пользователя
 * @return array Массив с данными найденного пользователя
 */
function search_user_by_email ($db_connect, $email) {
    $sql_query = "SELECT `email`, `password`, `name` FROM `users` WHERE `email` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "s", $email);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_assoc($result);
}

/**
 * Получает ID пользователя по емеилу
 * @param mysqli $db_connect Ресурс подключения
 * @param string $email Емеил пользователя
 * @return integer ID пользователя
 */
function get_user_id ($db_connect, $email) {
    $sql_query = "SELECT `id` FROM `users` WHERE `email` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "s", $email);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_row($result)[0];
}

/**
 * Получает ID проекта по его имени
 * @param mysqli $db_connect Ресурс подключения
 * @param string $project_name Имя проекта
 * @return integer ID проекта
 */
function get_project_id ($db_connect, $project_name) {
    $sql_query = "SELECT `id` FROM `projects` WHERE `name` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "s", $project_name);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_row($result)[0];
}

/**
 * Получает все проекты по ID пользователя
 * @param mysqli $db_connect Ресурс подключения
 * @param integer $user_id ID пользователя
 * @return array Массив с проектами
 */
function get_projects ($db_connect, $user_id) {
    $projects = [
        [
            "id" => 0,
            "name" => "Все"
        ]
    ];
    $sql_query = "SELECT `id`, `name` FROM `projects` WHERE `user_id` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "i", $user_id);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    $fetch = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($fetch as $project) {
        $projects[$project["id"]] = $project;
    }
    return $projects;
}

/**
 * Получает все задачи по ID пользователя
 * @param mysqli $db_connect Ресурс подключения
 * @param integer $user_id ID пользователя
 * @return array Массив с задачами
 */
function get_tasks ($db_connect, $user_id) {
    $sql_query = "SELECT `id`, `done_date`, `name`, `file`, `deadline`, `project_id` FROM `tasks` WHERE `user_id` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "i", $user_id);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Переключает задачу на выполненную с проставлением даты выполнения или
 * сбрасыает обратно на NULL по ID задачи
 * @param mysqli $db_connect Ресурс подключения
 * @param string $task_id ID задачи
 * @return string Значение поля даты выполнения задачи
 */
function toggle_done ($db_connect, $task_id) {
    $sql_query = "SELECT `done_date` FROM `tasks` WHERE `id` = ?";
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "i", $task_id);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result = mysqli_stmt_get_result($statement);
    $done_date = mysqli_fetch_row($result)[0];
    if ($done_date) {
        $sql_query = "UPDATE `tasks` SET `done_date` = NULL WHERE `id` = ?";
    } else {
        $sql_query = "UPDATE `tasks` SET `done_date` = NOW() WHERE `id` = ?";
    }
    $statement = mysqli_prepare($db_connect, $sql_query);
    mysqli_stmt_bind_param($statement, "i", $task_id);
    $execute = mysqli_stmt_execute($statement);
    if (!$execute) {
        print(mysqli_error($db_connect));
        exit;
    }
    return $done_date;
}
?>
