<?php
require_once("functions.php");

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$projects = [
    "Все",
    "Входящие",
    "Учеба",
    "Работа",
    "Домашние дела",
    "Авто"
];

$tasks = [
    [
        "task" => "Собеседование в IT компании",
        "date" => "01.06.2018",
        "category" => $projects[3],
        "is_completed" => false
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date" => "25.05.2018",
        "category" => $projects[3],
        "is_completed" => false
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date" => "21.04.2018",
        "category" => $projects[2],
        "is_completed" => true
    ],
    [
        "task" => "Встреча с другом",
        "date" => "22.04.2018",
        "category" => $projects[1],
        "is_completed" => false
    ],
    [
        "task" => "Купить корм для кота",
        "date" => "08.02.2018",
        "category" => $projects[4],
        "is_completed" => false
    ],
    [
        "task" => "Заказать пиццу",
        "date" => "09.02.2018",
        "category" => $projects[4],
        "is_completed" => false
    ]
];

function get_tasks_amount ($tasks, $project) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project === "Все") {
            $count = count($tasks);
        }
        if ($task["category"] === $project) {
            $count++;
        }
    }
    return $count;
}

function get_urgent_task ($date) {
    $current_timestamp = time();
    $task_timestamp = strtotime($date);
    $seconds_in_day = 86400;
    $difference = floor(($task_timestamp - $current_timestamp) / $seconds_in_day);
    if ($difference < 1) {
        return true;
    }
    return false;
}

function filter_tasks ($tasks, $show_complete_tasks, $project = "Все") {
    $filtered_tasks = [];
    foreach ($tasks as $key => $task) {
        if ($show_complete_tasks) {
            if ($project === "Все") {
                $filtered_tasks = $tasks;
            }
            if ($project === $task["category"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        } else {
            if ($project === "Все" && !$task["is_completed"]) {
                $filtered_tasks[] = $tasks[$key];
            }
            if ($project === $task["category"] && !$task["is_completed"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        }
    }
    return $filtered_tasks;
}

if (isset($_GET["project_id"])) {
    $PROJECT_ALL_TASKS = 0;
    $project_id = (int) $_GET["project_id"];
    $project_tasks = [];
    if ($project_id === $PROJECT_ALL_TASKS) {
        $project_tasks = filter_tasks($tasks, $show_complete_tasks);
    } elseif (isset($projects[$project_id])) {
        $project_tasks = filter_tasks($tasks, $show_complete_tasks, $projects[$project_id]);
    } else {
        http_response_code(404);
        $message = "Проектов с таким id не найдено.";
    }
} else {
    $project_tasks = filter_tasks($tasks, $show_complete_tasks);
}

if (http_response_code() === 404) {
    $page = set_template("templates/404.php", [
        "message" => $message
    ]);
} else {
    $page = set_template("templates/index.php", [
        "show_complete_tasks" => $show_complete_tasks,
        "project_tasks" => $project_tasks
    ]);
}

$layout = set_template("templates/layout.php", [
    "title" => "Дела в порядке",
    "content" => $page,
    "projects" => $projects,
    "tasks" => $tasks
]);

print($layout);
?>
