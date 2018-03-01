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
mysqli_set_charset($connection, "utf8");
if (!$connection) {
    print(mysqli_connect_error());
    exit;
}
?>
