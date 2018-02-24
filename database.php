<?php
$database = [
    "host" => "localhost",
    "user" => "root",
    "password" => "",
    "name" => "109619-doingsdone"
];

$connection = mysqli_connect(
    $database["host"],
    $database["user"],
    $database["password"],
    $database["name"]
);

if (!$connection) {
    print(mysqli_connect_error());
} else {
    $users_sql_query = "SELECT `email`, `password`, `name` FROM `users`";
    $users_result = mysqli_query($connection, $users_sql_query);
    if (!$users_result) {
        print(mysqli_error($connection));
    } else {
        $users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
    }
}
?>
