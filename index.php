<?php
session_start();

require_once("database.php");
require_once("functions.php");

$PROJECT_ALL_TASKS = 0;

$page = set_template("templates/guest.php", []);
$modal = null;
$user_id = (isset($_SESSION["user"])) ? get_user_id($connection, $_SESSION["user"]["email"]) : [];
$projects = (isset($_SESSION["user"])) ? get_projects($connection, $user_id) : [];
$tasks = (isset($_SESSION["user"])) ? get_tasks($connection, $user_id) : [];
$project_id = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_project"])) {
    $errors = [];
    $required_fields = [
        "name"
    ];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = "Поле обязательно для заполнения";
        }
    }
    if (count($errors)) {
        $modal = set_template("templates/add_project.php", [
            "errors" => $errors
        ]);
    } else {
        add_project($connection, $user_id, $_POST["name"]);
    }
}

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
    } else {
        $date = (!empty($_POST["date"])) ? $_POST["date"] : null;
        $file = (!empty($_FILES["preview"]["name"])) ? $_FILES["preview"] : null;
        add_task(
            $connection,
            $_POST["name"],
            upload_file($file),
            $date,
            get_user_id($connection, $_SESSION["user"]["email"]),
            get_project_id($connection, $_POST["project"])
        );
    }
}

if (isset($_GET["toggle_done"])) {
    $task_id = (int) $_GET["toggle_done"];
    toggle_done($connection, $task_id);
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_COOKIE["showcompl"])) {
    $show_complete_tasks = ((int) $_COOKIE["showcompl"] === 1) ? 0 : 1;
}

if (isset($_GET["show_completed"])) {
    setcookie("showcompl", $show_complete_tasks, strtotime("+30 days"), "/");
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET["filter"])) {
    setcookie("filter", $_GET["filter"], strtotime("+30 days"), "/");
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET["login"])) {
    $modal = set_template("templates/auth_form.php", []);
}

if (isset($_SESSION["user"])) {
    if (isset($_GET["project_id"])) {
        $project_id = (int) $_GET["project_id"];
        $project_tasks = [];
        if ($project_id === $PROJECT_ALL_TASKS) {
            $project_tasks = filter_tasks_by_project($tasks, $projects[$PROJECT_ALL_TASKS]["id"], $show_complete_tasks);
        } elseif (isset($projects[$project_id])) {
            $project_tasks = filter_tasks_by_project($tasks, $projects[$project_id]["id"], $show_complete_tasks);
        } else {
            http_response_code(404);
            $message = "Проектов с таким id не найдено.";
        }
    } else {
        $project_tasks = filter_tasks_by_project($tasks, $projects[$PROJECT_ALL_TASKS]["id"], $show_complete_tasks);
    }
    if (isset($_COOKIE["filter"])) {
        $filter = $_COOKIE["filter"];
        $project_tasks = filter_tasks_by_deadline($project_tasks, $filter);
    }
    if (isset($_GET["add_project"])) {
        $modal = set_template("templates/add_project.php", []);
    }
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
