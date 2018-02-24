<?php
if (!file_exists("dbconn.php")) {
    die("Ошибка подключения к базе данных. Проверьте наличие файла dbconn.php или его настройки.");
}
require_once("dbconn.php");

$connection = mysqli_connect(
    $dbconn["host"],
    $dbconn["user"],
    $dbconn["password"],
    $dbconn["name"]
);
if (!$connection) {
    print(mysqli_connect_error());
    exit;
}
$users_sql_query = "SELECT `email`, `password`, `name` FROM `users`";
$users_result = mysqli_query($connection, $users_sql_query);
if (!$users_result) {
    print(mysqli_error($connection));
    exit;
}
$users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
?>
