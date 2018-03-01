<?php
session_start();

require_once("database.php");
require_once("functions.php");

$PROJECT_ALL_TASKS = 0;

if (isset($_COOKIE["showcompl"])) {
    $show_complete_tasks = ((int) $_COOKIE["showcompl"] === 1) ? 0 : 1;
}

$page = set_template("templates/guest.php", []);
$modal = null;
$user_id = (isset($_SESSION["user"])) ? get_user_id($connection, $_SESSION["user"]["email"]) : null;
$projects = (isset($_SESSION["user"])) ? get_projects($connection, $user_id) : null;
$tasks = (isset($_SESSION["user"])) ? get_tasks($connection, $user_id) : null;
$project_id = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_task"])) {
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
        $modal = set_template("templates/add_task.php", [
            "errors" => $errors,
            "projects" => array_slice($projects, 1)
        ]);
    }
    if (empty($_POST["date"])) {
        $format_date = null;
    } else {
        $format_date = date("d.m.Y", strtotime($_POST["date"]));
    }
    // array_unshift($tasks, [
    //     "task" => $_POST["name"],
    //     "date" => $format_date,
    //     "category" => $_POST["project"],
    //     "file_name" => $_FILES["preview"]["name"],
    //     "file_url" => upload_file($_FILES["preview"]),
    //     "is_completed" => false
    // ]);
}

if (isset($_GET["show_completed"])) {
    setcookie("showcompl", $show_complete_tasks, strtotime("+30 days"), "/");
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET["project_id"])) {
    $project_id = (int) $_GET["project_id"];
    $project_tasks = [];
    if ($project_id === $PROJECT_ALL_TASKS) {
        $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS]["name"], $show_complete_tasks);
    } elseif (isset($projects[$project_id])) {
        $project_tasks = filter_tasks($tasks, $projects[$project_id]["name"], $show_complete_tasks);
    } else {
        http_response_code(404);
        $message = "Проектов с таким id не найдено.";
    }
} else {
    $project_tasks = filter_tasks($tasks, $projects[$PROJECT_ALL_TASKS]["name"], $show_complete_tasks);
}

if (isset($_GET["login"])) {
    $modal = set_template("templates/auth_form.php", []);
}

if (isset($_SESSION["user"])) {
    if (isset($_GET["add_task"])) {
        $modal = set_template("templates/add_task.php", [
            "projects" => array_slice($projects, 1)
        ]);
    }
    if (http_response_code() === 404) {
        $page = set_template("templates/404.php", [
            "message" => $message
        ]);
    } else {
        $page = set_template("templates/index.php", [
            "project_tasks" => $project_tasks,
            "show_complete_tasks" => $show_complete_tasks
        ]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $errors = [];
    $required_fields = [
        "email",
        "password"
    ];
    $user = search_user_by_email($connection, $_POST["email"]);
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = "Поле обязательно для заполнения";
        }
    }
    if (!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "E-mail введён некорректно";
    } elseif (!empty($_POST["email"]) && !$user) {
        $errors["email"] = "Пользователь не найден";
    }
    if (!empty($_POST["password"]) && !password_verify($_POST["password"], $user["password"])) {
        $errors["password"] = "Пароль введён неверно";
    }
    if (count($errors)) {
        $modal = set_template("templates/auth_form.php", [
            "errors" => $errors
        ]);
    } else {
        $_SESSION["user"] = $user;
        header("Location: /");
    }
}

if (isset($_GET["register"])) {
    $page = set_template("templates/register.php", []);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    require_once("register.php");
}

if (isset($_GET["logout"])) {
    require_once("logout.php");
}

$layout = set_template("templates/layout.php", [
    "title" => "Дела в порядке",
    "content" => $page,
    "modal" => $modal,
    "projects" => $projects,
    "project_id" => $project_id,
    "tasks" => $tasks
]);

print($layout);
?>
