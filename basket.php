<?php
require_once __DIR__."/config.php";

if (!check_auth()) header("Location: /login.php");

// Удаление товара из корзины при POST запросе
if (isset($_POST["id"])) {
    if (ctype_digit($_POST["id"]) || is_int($_POST["id"])) {
        $db->query("DELETE FROM `basket` WHERE `id` = ".$_POST["id"]." and `user_id` = ".$user["id"]);
    };
};

// Запрос всех товаров корзины пользователя
$query = $db->prepare("SELECT * FROM `basket` WHERE `user_id` = :id");
$query->execute(["id" => $user["id"]]);
$basket = $query->fetchAll();
$products = [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/basket.css">
    <script src="js/basket.js"></script>
</head>
<body>
    <?php include_once __DIR__."/header.php"; ?>
    <?php
    if (count($basket)) {
        foreach($basket as $value) {
            // Проверка наличия id в массиве $products
            if (!in_array($value["product_id"], $products)) {
                // Запрос информации о товаре
                $query = $db->query("SELECT * FROM `products` WHERE `id` = ".$value["product_id"]);
                // Запись информации в массив 
                $products[$value["product_id"]] = $query->fetch();
            };
            echo <<< EOT
            <div class="basket">
                <img src="{$products[$value['product_id']]['preview']}">
                <div class="name">{$products[$value['product_id']]['name']}</div>
                <div class="price">{$products[$value['product_id']]['price']} ₽</div>
                <button id="{$value['id']}" class="basket-cancel">Удалить из корзины</button>
            </div>
            EOT;
        };
    } else {
        echo '<h1>Корзина пустая!</h1>';
    };
    ?>
    <div id="confirm">
        <p>Подтверждение отмены заказа</p>
        <form action="" method="POST">
            <input type="hidden" name="id" id="id-basket" value="">
            <p class="name">Товар: </p>
            <p class="price">Цена:  р.</p>
            <div>
                <button type="button" id="close-confirm">Отмена</button>
                <input type="submit" value="Удалить из корзины" disabled>
            </div>
        </form>
    </div>
</body>
</html>