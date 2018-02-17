<?php
if (isset($_SESSION["user"])) {
    $_SESSION = [];
    header("Location: /");
}
?>
