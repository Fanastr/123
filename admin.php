<?php
require_once __DIR__."/config.php";

// Проверка авторизации
if (check_auth()) {
    if (!$user["admin"]) {
        header("Location: /");
    };
} else {
    header("Location: /");
};

// Массив в котором указаны разрешённые url адреса для админки 
$create_list = ["product", "category", "type"];
// Название таблицы в базе по url
$sql_name_table = [
    "product" => "products",
    "category" => "categories",
    "type" => "type_product",
];

// Если url имеет неправильную структуру перенаправление на создание товара
if (count($request_url) < 2) {
    header("Location: /admin.php/product");
};

// Из url берётся нужное значение для обозначения текущей таблицы
$table_name = $request_url[1];

// Если таблицы нет в массиве $create_list, перенаправление на создание товара
if (!in_array($table_name, $create_list)) {
    header("Location: /admin.php/product");
};

// Переменная для вывода сообщения
$message = "";
// Переменная для структурирования данных для вставки значений в SQL запрос
$data = [];

// Список обязательных полей для таблиц
$required_fields = [
    "product" => ["name", "description", "price", "category_id", "type_id"],
    "category" => ["name"],
    "type" => ["name", "url"],
];
// Переменная для обработки полученных полей
$fields = [];

if ($end_request_url == "create") {
    // Присвоение $data полученного POST запроса
    $data = $_POST;
    // SQL запросы для таблиц
    $insert_text = [
        "product" => "INSERT INTO `products` (`name`, `description`, `price`, `category_id`, `type_id`, `preview`) VALUES (:name, :description, :price, :category_id, :type_id, :preview)",
        "category" => "INSERT INTO `categories` (`name`) VALUES (:name)",
        "type" => "INSERT INTO `type_product` (`name`, `url`) VALUES (:name, :url)",
    ];
    
    // Перебор обязательных полей и проверка существования их в полученном запросе
    foreach($required_fields[$table_name] as $field) {
        if (isset($_POST[$field])) {
            $fields[$field] = $_POST[$field];
        } else {
            $message = "Заполните все поля!";
            break;
        };
    };
    
    // Если нет картинки и текущая таблица "product" присвоение переменной сообщения об ошибке
    if (!isset($_FILES["preview"]) && $table_name == "product") {
        $message = "Заполните все поля!";
    };
    
    // Если сообщений нет, то обрабатываем запрос
    if (!$message) {
        if ($table_name == "product") {
            // Обработка полученного файла 
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $ext = array_search(
                $finfo->file($_FILES['preview']['tmp_name']),
                [
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ],
                true
            );
        
            // Создание ссылки на сохранённый файл
            $fields["preview"] = "/uploads/".bin2hex(random_bytes(5)).".".$ext;
        
            // Сохранение файла под новым именем на сервере
            move_uploaded_file($_FILES["preview"]["tmp_name"],__DIR__.$fields["preview"]);
        };
        
        // SQL запрос
        $query = $db->prepare($insert_text[$table_name]);
        $query->execute($fields);

        // Если был создан товара, переход на страницу созданного товара
        if ($table_name == "product") {
            header("Location: /more_detailed.php?id={$db->lastInsertId()}");
        };
    };
} else if ($end_request_url == "update") {
    // Получение ID из запроса
    $id = $_GET["id"] ?? $_POST["id"] ?? -1;

    if ($id >= 0) {
        $_POST["id"] = $id;

        // SQL запросы для обновления данных
        $update_text = [
            "product" => "UPDATE `products` SET `name` = :name, `description` = :description, `price` = :price, `category_id` = :category_id, `type_id` = :type_id, `preview` = :preview WHERE `id` = :id",
            "category" => "UPDATE `categories` SET `name` = :name WHERE `id` = :id",
            "type" => "UPDATE `type_product` SET `name` = :name, `url` =  :url WHERE `id` = :id",
        ];

        // Переменная для проверки всех полей
        $is_update = true;

        // Перебор необходимых полей для текущей таблицы и проверка существование поля в запросе
        foreach($required_fields[$table_name] as $field) {
            if (!isset($_POST[$field])) {
                $is_update = false;
                break;
            };
        };

        if ($table_name == "product" && $is_update) {
            // Запрос товара
            $query = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$id);
            $data = $query->fetch();

            // Добавление информации о картинки
            $_POST["preview"] = $data["preview"];

            // Если картинка меняется, загрузка картинки на сервер и обновление данных в БД
            if (!$_FILES["preview"]["error"]) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $ext = array_search(
                    $finfo->file($_FILES['preview']['tmp_name']),
                    [
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ],
                    true
                );
            
                $name_file = explode("/", $data["preview"]);
                $name_file = explode(".", end($name_file));
                if ($name_file[1] == $ext) {
                    $_POST["preview"] = "/uploads/".$name_file[0].".".$ext;
                } else {
                    $_POST["preview"] = "/uploads/".bin2hex(random_bytes(5)).".".$ext;
                };
            
                move_uploaded_file($_FILES["preview"]["tmp_name"], __DIR__.$_POST["preview"]);
            };
        };

        // Выполение SQL запроса
        if ($is_update) {
            $query = $db->prepare($update_text[$table_name]);
            $query->execute($_POST);
        };

        // Получение обновлённых данных
        $query = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$id);
        $data = $query->fetch();
    };
} else if ($end_request_url == "remove") {
    if ($_POST["id"] ?? false) {
        // Удаление записи из БД
        $query = $db->query("DELETE FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$_POST["id"]);

        // Перенаправления
        if ($table_name == "product") {
            header("Location: /");
        } else {
            header("Location: /admin.php/".$table_name);
        };
    } else if ($_GET["id"] ?? false) {
        // Получение данных из БД
        $query = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$_GET["id"]);
        $data = $query->fetch();
    };
};

// Получение всех категорий
$query = $db->query("SELECT * FROM `categories`");
$categories = $query->fetchAll();

// Получение всех типов товаров
$query = $db->query("SELECT * FROM `type_product`");
$types = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <header>
        <a href="/">Главная</a>
        <?php
            // Кнопки навигации

            if ($end_request_url == "product") {
                echo '<a href="#" class="active">Добавить товар</a>';
            } else {
                echo '<a href="/admin.php/product">Добавить товар</a>';
            };

            if ($end_request_url == "category") {
                echo '<a href="#" class="active">Категории товаров</a>';
            } else {
                echo '<a href="/admin.php/category">Категории товаров</a>';
            };

            if ($end_request_url == "type") {
                echo '<a href="#" class="active">Типы товаров</a>';
            } else {
                echo '<a href="/admin.php/type">Типы товаров</a>';
            };
        ?>
    </header>
    <?php
    if ($message) {
        echo '<div class="error-mes">'.$message.'</div>';
    };
    ?>
    <?php
    if ($end_request_url == "update" || $end_request_url == "remove") {
        // Если в запросе есть id вывод формы, иначе вывод сообщения
        if ($_GET["id"] ?? $_POST["id"] ?? false) {
            include_once __DIR__."/forms/".$end_request_url."_".$table_name.".php";
        } else {
            echo '<h1>Запись не найдена!</h1>';
        };
    } else {
        include_once __DIR__."/forms/create_".$table_name.".php";
    };
    ?>
    <?php
    // Вывод списков элементов некоторых таблиц
    if ($table_name == "category" || $table_name == "type") {
        // Присвоение нужного списка элементов
        $items = $table_name == "category" ? $categories : $types;
        echo '<div class="'.$table_name.'">';
        foreach($items as $value) {
            echo <<< EOT
            <a href="/admin.php/{$table_name}/update?id={$value['id']}">{$value['name']}</a>
            EOT;
        };
        echo "</div>";
    };
    ?>
</body>
</html>