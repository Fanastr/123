<?php
require_once __DIR__."/config.php";

if (check_auth()) {
    header("Location: /");
};

// Переменные для проврки полученных данных
$len_username = 1;
$len_password = 1;
$username_message = "";
$password_message = "";

// Проверка существование необходимых значений в запросе
if (isset($_POST["username"], $_POST["password"])) {
    // Удаление лишних пробелов в начале строки и в конце
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Количество символов
    $len_username = strlen($username);
    $len_password = strlen($password);

    // Если полученные значения не пустые
    if ($len_username && $len_password) {
        // Запрос пользователя по имени
        $query = $db->prepare("SELECT * FROM `users` WHERE `username` = :username");
        $query->execute(["username" => $username]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        // Если пользователь есть, проверяем правильность введённого пароля и переходим на главную
        // Иначе вывод сообщения об ошибке
        if ($user) {
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                header("Location: /");
            } else {
                $password_message = "Неверный пароль!";
            };
        } else {
            $username_message = "Пользователь не найден!";
        };
    };
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <header>
        <a href="/">Главная</a>
    </header>
    <div class="content">
        <form action="/login.php" method="POST">
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
            <input type="submit" value="Войти">
            <a href="/registration.php">Регистрация</a>
        </form>
    </div>
</body>
</html>