<?php
require_once("functions.php");

$PROJECT_ALL_TASKS = 0;

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

if (isset($_GET["project_id"])) {
    $project_id = (int) $_GET["project_id"];
    $project_tasks = [];
    if ($project_id === $PROJECT_ALL_TASKS) {
        $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS], $show_complete_tasks);
    } elseif (isset($projects[$project_id])) {
        $project_tasks = filter_tasks($tasks, $projects[$project_id], $show_complete_tasks);
    } else {
        http_response_code(404);
        $message = "Проектов с таким id не найдено.";
    }
} else {
    $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS], $show_complete_tasks);
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
