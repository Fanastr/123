<?php
// Подключение php файла
// __DIR__ - текущий каталог
require_once __DIR__."/config.php";

if (!check_auth()) header("Location: /login.php");

// Получение избранных товаров у пользователя с сортировкой от новых к старым
$query = $db->query("SELECT * FROM `favorites` WHERE `user_id` = ". $user["id"] ." ORDER BY -`id`");
$favorites = $query->fetchAll(PDO::FETCH_ASSOC)
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
    <?php include_once __DIR__."/header.php"; ?>
    <?php
    if ($favorites) {
        echo <<< EOT
            <div class="block">
                <h2>Изранное</h2>
                <div class="slider">
        EOT;
        foreach ($favorites as $value) {
            // Запрос товара
            $query = $db->query("SELECT * FROM `products` WHERE `id` = ".$value["product_id"]);
            $product = $query->fetch(PDO::FETCH_ASSOC);
            // Проверка существования товара
            if (!$product) continue;
            // EOT - начало и конец строки
            echo <<< EOT
                    <a href="/more_detailed.php?id={$product['id']}" class="slider__item">
                        <div class="img-container">
                            <img src="{$product['preview']}">
                        </div>
                        <p>{$product['name']}</p>
                    </a>
            EOT;
        };
        echo <<< EOT
                </div>
            </div>
        EOT;
    } else {
        echo <<< EOT
        <div class="block">
            <h2>Изранное</h2>
            <div class="slider"></div>
        </div>
        <h1>Товары не найдены!</h1>
        EOT;
    };
    ?>
</body>
</html>