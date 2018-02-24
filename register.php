<?php
require_once("mysql_helper.php");

$errors = [];
$required_fields = [
    "email",
    "password",
    "name"
];
$user = search_user_by_email($users, $_POST["email"]);
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[$field] = "Поле обязательно для заполнения";
    }
}
if (!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "E-mail введён некорректно";
}
if ($_POST["email"] === $user["email"]) {
    $errors["email"] = "Этот e-mail уже используется";
}
if (count($errors)) {
    $page = set_template("templates/register.php", [
        "errors" => $errors
    ]);
} else {
    $user_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $save_user_sql_query = "INSERT INTO `users` SET `register_date` = NOW(), `email` = ?, `password` = ?, `name` = ?";
    $save_user_statement = db_get_prepare_stmt($connection, $save_user_sql_query, [$_POST["email"], $user_password, $_POST["name"]]);
    $save_user_result = mysqli_stmt_execute($save_user_statement);
    if (!$save_user_result) {
        print(mysqli_error($connection));
        exit;
    } else {
        header("Location: /?login");
    }
}
?>
