<?php
// Подключение php файла
// __DIR__ - текущий каталог
require_once __DIR__."/config.php";

// Фильтер для поиска по товарам
$filter = "";

if (isset($_GET["search"])) {
    $filter = "WHERE `name` like '%". $_GET["search"] ."%'";
};

// Проверка есть ли в запросе категория
if (isset($_GET["category"])) {
    // Проверка является ли значение числом
    if (ctype_digit($_GET["category"])) {
        // Добавляем условие в запрос
        $filter = "WHERE `category_id` = ". $_GET["category"];
    };
};

// Получение информации о текущем типе товаров
$type = $db->query("SELECT * FROM `type_product` WHERE `url` = '".($active_type ? $active_type : $end_request_url)."'")->fetch(PDO::FETCH_ASSOC);
if ($type) {
    $type = $type["id"];
} else {
    $type = $db->query("SELECT * FROM `type_product` LIMIT 1")->fetch(PDO::FETCH_ASSOC)["id"];
};

// Добавление условия по типу к запросу
if ($type) {
    if ($filter) {
        $filter = $filter." and `type_id` = ".$type;
    } else {
        $filter = "WHERE `type_id` = ".$type;
    };
};

// Запрос из БД товаров с условием, если оно есть, с созданным условием
$query = $db->query("SELECT * FROM `products` ". $filter ." ORDER BY -`id`");
// Запрос массива с значениями
$products = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/index.css">
</head>
<body>
    <!-- Полключение шапки сайта -->
    <?php include_once __DIR__."/header.php"; ?>
    <?php
    if (count($products)) {
        echo <<< EOT
            <div class="block">
                <h2>Товары</h2>
                <div class="slider">
        EOT;
        foreach ($products as $value) {
            // EOT - начало и конец строки
            echo <<< EOT
                    <a href="/more_detailed.php?id={$value['id']}" class="slider__item">
                        <div class="img-container">
                            <img src="{$value['preview']}">
                        </div>
                        <p>{$value['name']}</p>
                    </a>
            EOT;
        };
        echo <<< EOT
                </div>
            </div>
        EOT;
    } else {
        echo <<< EOT
        <h1>Товар не найден!</h1>
        EOT;
    };
    ?>

    <script>
        // Подключение к событию загрузки контента страницы
        window.addEventListener("DOMContentLoaded", () => {
            // Получение input поиска
            const searchInput = document.querySelector("input[type='search']");

            // Проверка существования input
            if (searchInput) {
                // Получение текущего значения поиска и присвоение значения в input
                const getParam = new URLSearchParams(window.location.search);
                searchInput.value = getParam.get("search") || "";
            };
        });
    </script>
</body>
</html>