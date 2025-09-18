<?php
session_start();
$users = json_decode(file_get_contents('users_log.json'), true);

if(isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $found = false;

    foreach($users['users'] as $u){
        if($u['email'] === $email && $u['password'] === $pass){
            $_SESSION['user'] = $email;
            $found = true;
            header("Location: toko.php");
            exit;
        }
    }
    $error = $found ? "" : "Email atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Toko Adrian</title>
</head>
<body>
<h2>Login User</h2>
<?php if(!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
