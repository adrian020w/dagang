<?php
session_start();

// File data user
$users_file = 'users_log.json';
if(!file_exists($users_file)) file_put_contents($users_file, json_encode(['users'=>[]]));
$users_data = json_decode(file_get_contents($users_file), true);
$users = [];
if(isset($users_data['users'])){
    foreach($users_data['users'] as $u){
        $users[$u['email']] = $u['password'];
    }
}

$login_error = '';
if(isset($_POST['login'])){
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    if(isset($users[$email]) && $users[$email] === $pass){
        $_SESSION['user_email'] = $email;

        // Catat login
        $users_data['users'] = array_map(function($u) use ($email){
            if($u['email']===$email) $u['last_login']=date('Y-m-d H:i:s');
            return $u;
        }, $users_data['users']);
        file_put_contents($users_file, json_encode($users_data, JSON_PRETTY_PRINT));

        header('Location: toko.php');
        exit;
    } else {
        $login_error = "Email atau password salah!";
    }
}

// Form login
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login User - Toko Adrian</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; }
form { background:#fff; padding:30px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1); width:300px; text-align:center; }
input { width:100%; padding:10px; margin:10px 0; border-radius:8px; border:1px solid #ccc; }
button { padding:10px 20px; border:none; border-radius:8px; background:#06b6d4; color:#fff; font-weight:600; cursor:pointer; }
.error { color:red; margin-bottom:10px; }
</style>
</head>
<body>
<form method="post">
<h2>Login User</h2>
<?php if($login_error) echo "<div class='error'>$login_error</div>"; ?>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>
</body>
</html>
