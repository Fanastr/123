<?php
if ($request_url == "header.php") {
    header("Location: /");
};

$auth = '<a href="/login.php">Войти</a>';
if (check_auth()) {
    $auth = '<a href="/logout.php">Выход</a>';
};

// Запрос категорий
$query = $db->query("SELECT * FROM `categories`");
$categories = $query->fetchAll();
?>
<header>
    <?php
        // Запрос типов товаров
        $query = $db->query("SELECT * FROM `type_product`");

        // Перебор типов
        foreach ($query->fetchAll() as $index=>$value) {
            $active = "";

            // Определение активного типа
            if ((($end_request_url == $value['url'] || (!$end_request_url && !$index)) && (!$active_type)) || ($active_type == $value['url'])) {
                $active_type = $value['url'];
                $active = "class='active'";
            }

            // EOT - начало и конец строки
            echo <<< EOT
            <a href="/index.php/{$value['url']}" {$active}>{$value['name']}</a>
            EOT;
        };

        if (check_auth()) {
            if ($user["admin"]) {
                echo '<a href="/admin.php">Админ панель</a>';
            };
        };
    ?>
    <div class="header-right">
        <form action="/index.php/<?php echo $active_type; ?>" method="get">
            <input type="search" name="search" placeholder="Поиск">
        </form>
        <a href="/favorites.php" <?php if ($end_request_url == "favorites.php") echo "class='active'"; ?>>Избранное</a>
        <a href="/basket.php" <?php if ($end_request_url == "basket.php") echo "class='active'"; ?>>Корзина</a>
        <?php echo $auth ?>
    </div>
</header>
<div class="nav">
    <!-- <a href="">Новинки</a> -->
    <a href="">Одежда</a>
    <div class="info">
        <div>
            <div class="title">Категории</div>
            <ul>
                <?php
                    foreach($categories as $category) {
                        echo <<< EOT
                        <li><a href="?category={$category['id']}">{$category['name']}</a></li>
                        EOT;
                    };
                ?>
            </ul>
        </div>
    </div>
</div>