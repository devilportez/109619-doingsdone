<?php
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
} else {
    $users_sql_query = "SELECT `email`, `password`, `name` FROM `users`";
    $users_result = mysqli_query($connection, $users_sql_query);
    if (!$users_result) {
        print(mysqli_error($connection));
        exit;
    } else {
        $users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
    }
}
?>
