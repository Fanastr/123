<?php
require_once __DIR__."/config.php";

$product = null;
$buy = false;

// Проверка значение в POST
if (isset($_POST["id"], $_POST["name"])) {
    // Переход на авторизацию, если пользователь не авторизован и в POST запросе "name" != "favorites"
    if ($_POST["name"] != "favorites" || !check_auth()) header("Location: /login.php");

    $id = $_POST["id"];

    // Запрос избранного товара по полученным данных
    $query = $db->prepare("SELECT * FROM `favorites` WHERE `user_id` = :user_id and `product_id` = :product_id");
    $query->execute([
        "user_id" => $user["id"],
        "product_id" => $id
    ]);
    $favorites = $query->fetch(PDO::FETCH_ASSOC);

    // Если товара нет в изранном добавляем, иначе удаляем из избранного
    if (!$favorites) {
        $query = $db->prepare("INSERT INTO `favorites` (`user_id`, `product_id`) VALUES (:user_id, :product_id)");
        $query->execute([
            "user_id" => $user["id"],
            "product_id" => $id
        ]);
        echo 0;
        return;
    } else {
        $query = $db->prepare("DELETE FROM `favorites` WHERE `user_id` = :user_id and `product_id` = :product_id");
        $query->execute([
            "user_id" => $user["id"],
            "product_id" => $id
        ]);
        echo 1;
        return;
    };
};

if (isset($_GET["id"]) || isset($_POST["id"])) {
    // Получение ID из POST запроса, если в POST запросе нет ID, то обращаемся к GET
    $id = $_POST["id"] ?? $_GET["id"];

    // Проверка ID на число
    if (ctype_digit($id) || is_int($id)) {
        // Запрос товара
        $query = $db->query("SELECT * FROM `products` WHERE `id` = ".$id);
        $product = $query->fetch(PDO::FETCH_ASSOC);

        // Проверка POST запроса на ID
        if (isset($_POST["id"])) {
            if (check_auth()) {
                // Добавление в корзину
                $query = $db->prepare("INSERT INTO `basket` (`user_id`, `product_id`) VALUES (:user_id, :product_id)");
                $query->execute([
                    "user_id" => $user["id"],
                    "product_id" => $product["id"]
                ]);
                header("Location: /basket.php");
            };
        };
    };
};

$favorites = '';

// Если товар существует
if ($product) {
    // Получение значение типа
    $active_type = $db->query("SELECT * FROM `type_product` WHERE `id` = ".$product["type_id"])->fetch(PDO::FETCH_ASSOC)["url"];

    $favorites = '';

    // Проверка являеться ли товар избранным
    if ($user) {
        $query = $db->prepare("SELECT * FROM `favorites` WHERE `user_id` = :user_id and `product_id` = :product_id");
        $query->execute([
            "user_id" => $user["id"],
            "product_id" => $id
        ]);
        $favorites = $query->fetch(PDO::FETCH_ASSOC) ? 'bt-favorites--active' : '';
    };
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/more_detailed.css">
    <script src="js/more_detailed.js"></script>
</head>
<body>
    <input type="hidden" id="session_id" value="<?php if (check_auth()) echo $user["id"]; ?>">
    <?php include_once __DIR__."/header.php"; ?>
    <?php
        if ($product) {
            $bt_buy = '<button id="buy" class="bt-buy">Купить</button>';

            if (check_auth()) {
                if ($user["admin"]) {
                    echo <<< EOT
                    <div class="edit-links">
                        <a href="/admin.php/product/update?id={$product['id']}">Редактировать</a>
                        <a href="/admin.php/product/remove?id={$product['id']}">Удалить товар</a>
                    </div>
                    EOT;
                };
            } else {
                $bt_buy = '<a href="/login.php" class="bt-buy">Купить</a>';
            };

            echo <<< EOT
            <div class="product-continer">
                <img src="{$product['preview']}">
                <div>
                    <h1>{$product['name']}</h1>
                    <span class="price">{$product['price']} ₽</span>
                    $bt_buy
                    <button id="{$product['id']}" class="bt-favorites {$favorites}"></button>
                    <p class="description">{$product['description']}</p>
                </div>
            </div>
            EOT;

            include_once __DIR__."/forms/confirm_purchase.php";
        } else {
            echo <<< EOT
            <h1 class="not-product">Товар не найден!</h1>
            EOT;
        };
    ?>

    <script>
        window.addEventListener("DOMContentLoaded", () => {
            // Получение нужных элементов с страницы
            const btFavorites = document.querySelector(".bt-favorites");
            const inputSessionID = document.querySelector("#session_id");

            if (!btFavorites) return;

            // Создание формы и добавление необходимых данных для отправки
            const data = new FormData();
            if (inputSessionID.value) data.append("session_id", inputSessionID.value);
            data.append("id", +btFavorites.id);
            data.append("name", "favorites");

            // Действие при нажатии на кнопку
            btFavorites.onclick = () => {
                // Если пользователь авторизован можно добавлять/убирать избранность товара
                if (inputSessionID.value) {
                    // Отправка запроса на сервер для добавления/удаления товара в/из избранного
                    fetch("", {
                        method: "POST",
                        body: data,
                    }).then(response => {
                        return response.text();
                    }).then(text => {
                        if (text == "1") btFavorites.classList.remove("bt-favorites--active");
                        else btFavorites.classList.add("bt-favorites--active");
                    });
                } else {
                    window.location = "/login.php";
                };
            };
        });
    </script>
</body>
</html>