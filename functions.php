<?php
function set_template ($template, $data) {
    if (file_exists($template)) {
        extract($data);
        ob_start();
        require_once($template);
        return ob_get_clean();
    }
    return "";
}

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

function get_urgent_task ($date) {
    date_default_timezone_set("Europe/Moscow");
    $current_timestamp = time();
    $task_timestamp = strtotime($date);
    $seconds_in_day = 86400;
    $difference = floor(($task_timestamp - $current_timestamp) / $seconds_in_day);
    if ($difference < 1) {
        return true;
    }
    return false;
}

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
        } elseif ($filter === "today" && $difference > -2 && $difference < 0) {
            $filtered_tasks[] = $tasks[$key];
        } elseif ($filter === "tomorrow" && $difference > -1 && $difference < 1) {
            $filtered_tasks[] = $tasks[$key];
        } elseif ($filter === "overdue" && $difference < -1) {
            $filtered_tasks[] = $tasks[$key];
        }
    }
    return $filtered_tasks;
}

function upload_file ($file) {
    if (isset($file["name"])) {
        $file_name = $file["name"];
        $file_path = __DIR__ . "/uploads/";
        $file_url = "/uploads/" . $file_name;
        move_uploaded_file($file["tmp_name"], $file_path . $file_name);
    }
    return $file_url;
}

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

function toggle_done ($db_connect, $task_id) {
    $sql_query_get = "SELECT `done_date` FROM `tasks` WHERE `id` = ?";
    $statement_get = mysqli_prepare($db_connect, $sql_query_get);
    mysqli_stmt_bind_param($statement_get, "i", $task_id);
    $execute_get = mysqli_stmt_execute($statement_get);
    if (!$execute_get) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result_get = mysqli_stmt_get_result($statement_get);
    $done_date = mysqli_fetch_row($result_get)[0];
    if ($done_date) {
        $sql_query_set = "UPDATE `tasks` SET `done_date` = NULL WHERE `id` = ?";
    } else {
        $sql_query_set = "UPDATE `tasks` SET `done_date` = NOW() WHERE `id` = ?";
    }
    $statement_set = mysqli_prepare($db_connect, $sql_query_set);
    mysqli_stmt_bind_param($statement_set, "i", $task_id);
    $execute_set = mysqli_stmt_execute($statement_set);
    if (!$execute_set) {
        print(mysqli_error($db_connect));
        exit;
    }
    $result_set = mysqli_stmt_get_result($statement_set);
    $done_date = mysqli_fetch_row($result_set)[0];
    return $done_date;
}
?>
