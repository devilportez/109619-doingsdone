<?php
require_once("functions.php");

$PROJECT_ALL_TASKS = 0;

$show_complete_tasks = 1;
$add_task = null;

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

if (isset($_GET["add_task"])) {
    $add_task = set_template("templates/modal-task.php", [
        "projects" => array_slice($projects, 1)
    ]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_task'])) {
        $errors = [];
        $required_fields = [
            "name",
            "project"
        ];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = "Поле обязательно для заполнения";
            }
        }
        if (count($errors)) {
            $add_task = set_template("templates/modal-task.php", [
                "errors" => $errors,
                "projects" => array_slice($projects, 1)
            ]);
        }
        if (empty($_POST["date"])) {
            $format_date = null;
        } else {
            $format_date = date("d.m.Y", strtotime($_POST["date"]));
        }
        array_unshift($tasks, [
            "task" => $_POST["name"],
            "date" => $format_date,
            "category" => $_POST["project"],
            "file_name" => $_FILES["preview"]["name"],
            "file_url" => upload_file($_FILES["preview"]),
            "is_completed" => false
        ]);
    }
}

if (isset($_GET["show_completed"])) {
    if (isset($_COOKIE["showcompl"])) {
        $show_complete_tasks = ($_COOKIE["showcompl"] == 1) ? 0 : 1;
    }
    setcookie("showcompl", $show_complete_tasks, strtotime("+30 days"), "/");
    header("Location: /109619-doingsdone/index.php");
}

if (isset($_GET["project_id"])) {
    $project_id = (int) $_GET["project_id"];
    $project_tasks = [];
    if ($project_id === $PROJECT_ALL_TASKS) {
        $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS], $_COOKIE["showcompl"]);
    } elseif (isset($projects[$project_id])) {
        $project_tasks = filter_tasks($tasks, $projects[$project_id], $_COOKIE["showcompl"]);
    } else {
        http_response_code(404);
        $message = "Проектов с таким id не найдено.";
    }
} else {
    $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS], $_COOKIE["showcompl"]);
}

if (http_response_code() === 404) {
    $page = set_template("templates/404.php", [
        "message" => $message
    ]);
} else {
    $page = set_template("templates/index.php", [
        "show_complete_tasks" => $_COOKIE["showcompl"],
        "project_tasks" => $project_tasks
    ]);
}

$layout = set_template("templates/layout.php", [
    "title" => "Дела в порядке",
    "content" => $page,
    "projects" => $projects,
    "tasks" => $tasks,
    "add_task" => $add_task
]);

print($layout);
?>
