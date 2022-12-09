<?php
require_once __DIR__."/config.php";

if (check_auth()) {
    // Очищение значения в сессии
    unset($_SESSION["user_id"]);
};

header("Location: /");
?>