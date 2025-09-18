<?php
session_start();

// File data barang
$data_file = 'data.json';
$data = json_decode(file_get_contents($data_file), true);

// File log user login
$log_file = 'users_log.json';
if(!file_exists($log_file)) file_put_contents($log_file, json_encode([]));

// Daftar user (email => password)
$users = [
    "user1@example.com" => "password123",
    "user2@example.com" => "qwerty"
];

$login_error = "";
if(isset($_POST['login'])){
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    if(isset($users[$email])){
        if($users[$email] === $pass){
            $_SESSION['user_email'] = $email;

            // Catat login
            $log = json_decode(file_get_contents($log_file), true);
            $log[$email] = ['last_login'=>date('Y-m-d H:i:s')];
            file_put_contents($log_file, json_encode($log, JSON_PRETTY_PRINT));

        } else {
            $login_error = "Password salah!";
        }
    } else {
        $login_error = "User tidak terdaftar!";
    }
}

if(!isset($_SESSION['user_email'])):
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<?php exit(); endif; ?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Toko Adrian 3D</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg,#f0f2f5,#e6ebf1); margin:0; padding:30px; }
h1 { text-align:center; font-size:2.4rem; color:#222; margin-bottom:30px; }
.grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:30px; }
.card { perspective: 1000px; }
.card-inner { background:#fff; border-radius:15px; padding:15px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); transition: transform 0.6s; transform-style: preserve-3d; display:flex; flex-direction: column; align-items:center; }
.card:hover .card-inner { transform: rotateY(15deg) rotateX(5deg) scale(1.05); }
.card img { width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:12px; backface-visibility: hidden; }
.nama { font-weight:600; font-size:1.1rem; margin-bottom:5px; }
.stok { color:#555; margin-bottom:10px; }
.button { padding:8px 16px; border:none; border-radius:8px; background: linear-gradient(90deg,#06b6d4,#3b82f6); color:#fff; font-weight:600; cursor:pointer; transition: background 0.3s; text-decoration:none; margin-bottom:5px; }
.button:hover { background: linear-gradient(90deg,#3b82f6,#06b6d4); }
.logout { position:fixed; top:20px; right:20px; background:red; color:#fff; padding:8px 12px; border:none; border-radius:6px; cursor:pointer; }
.add-stock { display:flex; gap:5px; margin-top:5px; }
.add-stock input { width:50px; padding:4px; border-radius:5px; border:1px solid #ccc; text-align:center; }
.add-stock button { padding:4px 8px; border-radius:5px; border:none; background:#10b981; color:#fff; cursor:pointer; transition: background 0.3s; }
.add-stock button:hover { background:#059669; }
@media(max-width:500px){ .card img { height:150px; } }
</style>
</head>
<body>

<h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
<button class="logout" onclick="location.href='logout.php'">Logout</button>
<div class="grid">
<?php foreach($data['barang'] as $index => $item): ?>
<div class="card">
<div class="card-inner" id="card-<?php echo $index; ?>">
<img src="<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
<div class="nama"><?php echo htmlspecialchars($item['nama']); ?></div>
<div class="stok" id="stok-<?php echo $index; ?>">Stok: <?php echo intval($item['stok']); ?></div>
<button class="button" onclick="beli(<?php echo $index; ?>)">Beli Sekarang</button>
<div class="add-stock">
<input type="number" min="1" value="1" id="add-<?php echo $index; ?>">
<button onclick="tambahStok(<?php echo $index; ?>)">Tambah</button>
</div>
</div>
</div>
<?php endforeach; ?>
</div>

<script>
function beli(idx){
    const stokEl = document.getElementById(`stok-${idx}`);
    let stok = parseInt(stokEl.textContent.replace('Stok: ',''));
    if(stok>0){
        stok--;
        stokEl.textContent = 'Stok: '+stok;
        alert('Berhasil beli produk!');
    } else {
        alert('Stok habis!');
    }
}
function tambahStok(idx){
    const input = document.getElementById(`add-${idx}`);
    const stokEl = document.getElementById(`stok-${idx}`);
    let tambah = parseInt(input.value);
    let stok = parseInt(stokEl.textContent.replace('Stok: ',''));
    stok += tambah;
    stokEl.textContent = 'Stok: '+stok;
    input.value = 1;
}
</script>

</body>
</html>
