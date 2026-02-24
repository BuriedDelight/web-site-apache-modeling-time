<?php
session_start();

// 1. Подключение к БД
$host = 'localhost';
$db   = 'my_website';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// 2. Логика входа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Проверяем, существует ли пользователь и совпадает ли хеш пароля
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Перенаправляем на главную после успешного входа
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизоваться - Modeling Time</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="reg-body">

    <header class="reg-header">
        <div class="logo">
            <img src="img/logo-clock.png" alt="Logo" class="logo-icon">
        </div>
        <h1 class="page-title">Авторизоваться</h1>
    </header>

    <main class="reg-container login-card">
        <form action="login.php" method="POST" class="reg-form">
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <div class="input-group">
                <input type="text" name="username" placeholder="Логин" required>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Пароль" required>
            </div>

            <div class="options-row">
                <label class="show-pass-label">
                    <input type="checkbox" onclick="togglePass()"> Показать пароль
                </label>
                <a href="#" class="forgot-pass">Забыли пароль?</a>
            </div>

            <div class="login-link-section">
                <span>Еще не зарегистрированы?</span>
                <a href="register.php" class="btn-small-login">Регистрация</a>
            </div>

            <button type="submit" class="btn-submit main-auth-btn">Авторизоваться</button>
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