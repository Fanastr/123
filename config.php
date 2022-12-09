<?php
session_start();
// Проверка авторизован ли пользователь
function check_auth() : bool {
    if (isset($_GET["session_id"])) return !!($_GET["session_id"] ?? false);
    return !!($_SESSION["user_id"] ?? false);
};

// Подключение к БД
$db = new PDO("sqlite:db.sqlite3");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = null;

// Если пользователь авторизован, запрос информации о нём из БД
if (check_auth()) {
    $query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $query->execute(["id" => $_SESSION["user_id"]]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
};

// Разбор URL в массив
$request_url = explode("/", preg_replace("/(^\/(.*)\/$|\/(.*))/", "$2$3", $_SERVER["REQUEST_URI"]));
// Получение конца URL
$end_request_url = explode("?", end($request_url))[0];

$active_type = "";
?>