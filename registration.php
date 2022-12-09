<?php
require_once __DIR__.'/config.php';

if (check_auth()) {
    header("Location: /");
};

$len_username = 1;
$len_password = 1;
$username_message = "";
$password_message = "";

// Проверяем существуют ли в POST запросе значения: username, password
if (isset($_POST["username"], $_POST["password"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Длина строк этих значений
    $len_username = strlen($username);
    $len_password = strlen($password);

    // Если значения не пустые
    if ($len_username && $len_password) {
        // stmt - с англ. "заявление"
        // Ищем в бд пользователя с полученым "username"
        $stmt = $db->query("SELECT * FROM `users` WHERE `username` = :username");
        // Выполняем запрос с указанием переменной "username"
        $stmt->execute(["username" => $username]);
        // Проверяем количество вернувшихся значений
        if ($stmt->fetchColumn() > 0) {
            $username_message = "Пользователь уже существует!";
        } else {
            // С помощью регулярного выражения проверяем, что в пароле и логине только буквы и цифры
            $username_valid = strlen(preg_replace("/[a-zа-яё\d]/i", "", $username)) == 0;
            $password_valid = strlen(preg_replace("/[a-zа-яё\d]/i", "", $password)) == 0;

            if ($username_valid && $password_valid) {
                if ($len_username > 2 && $len_password >= 6 && $len_password <= 32) {
                    // Вставяем в бд полученные данные
                    $stmt = $db->prepare("INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)");
                    $stmt->execute([
                        "username" => $username,
                        "password" => password_hash($password, PASSWORD_DEFAULT),
                    ]);
                    
                    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                    $stmt->execute(["username" => $username]);
                    // Записываем в значение текущей сессии id пользоватея
                    $_SESSION["user_id"] = $stmt->fetch(PDO::FETCH_ASSOC)["id"];
                    header("Location: /");
                } else {
                    if ($len_username <= 2) {
                        $username_message = "Логин должен иметь больше 2 символов!";
                    };
                    if ($len_password < 6 || $len_password > 32) {
                        $password_message = "Пароль должен иметь от 6 до 32 символов!";
                    };
                };
            } else {
                if (!$username_valid) {
                    $username_message = "Логин может состоять только из букв и цифр!";
                };
                if (!$password_valid) {
                    $password_message = "Пароль может состоять только из букв и цифр!";
                };
            };
        };
    };
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <header>
        <a href="/">Главная</a>
    </header>
    <div class="content">
        <form action="/registration.php" method="POST">
            <?php
                if ($username_message) {
                    echo "<div class='error-mes'>".$username_message."</div>";
                } else if (!$len_username) {
                    echo "<div class='error-mes'>Введите логин!</div>";
                };
            ?>
            <input type="text" name="username" value="<?php echo trim($_POST["username"] ?? "") ?>" placeholder="Логин">
            <?php
                if ($password_message) {
                    echo "<div class='error-mes'>".$password_message."</div>";
                } else if (!$len_password) {
                    echo "<div class='error-mes'>Введите пароль!</div>";
                };
            ?>
            <input type="password" name="password" value="<?php echo trim($_POST["password"] ?? "") ?>" placeholder="Пароль">
            <input type="submit" value="Зарегистрироваться">
            <a href="/login.php">Авторизация</a>
        </form>
    </div>
</body>
</html>