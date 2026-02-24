<?php
// 1. Подключение к БД (замените данные на свои)
$host = 'localhost';
$db   = 'my_website';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    $error = "Ошибка подключения: " . $e->getMessage();
}

// 2. Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Простая валидация
    if (strlen($password) < 6) {
        $error = "Пароль должен быть не менее 6 символов!";
    } else {
        // Хеширование пароля
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$login, $email, $hash]);
            $success = "Регистрация успешна! Теперь вы можете войти.";
        } catch (PDOException $e) {
            $error = "Ошибка: пользователь с таким логином или email уже существует.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Зарегистрироваться - Modeling Time</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="reg-body">

    <header class="reg-header">
        <div class="logo">
            <img src="img/logo-clock.png" alt="Logo" class="logo-icon">
        </div>
        <h1 class="page-title">Зарегистрироваться</h1>
    </header>

    <main class="reg-container">
        <form action="register.php" method="POST" class="reg-form">
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <div class="input-group">
                <input type="text" name="username" placeholder="Логин" required>
                <span class="hint">Латинские буквы и цифры</span>
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="E-mail" required>
                <span class="hint">Требуется для активации учетной записи</span>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Пароль" required>
                <span class="hint">Минимум 6 символов</span>
            </div>

            <div class="show-pass">
                <label>Показать пароль <input type="checkbox" onclick="togglePass()"></label>
            </div>

            <div class="login-link-section">
                <span>Уже зарегистрированы?</span>
                <a href="login.php" class="btn-small-login">Войти</a>
            </div>

            <button type="submit" class="btn-submit">Зарегистрироваться</button>
        </form>
    </main>

    <script>
        function togglePass() {
            var x = document.getElementById("password");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>