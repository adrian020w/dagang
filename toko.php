<?php
session_start();
if(!isset($_SESSION['user_email'])){
    header("Location: index.php");
    exit;
}

// Load data barang
$data_file = 'data.json';
if(!file_exists($data_file)) file_put_contents($data_file, json_encode(['barang'=>[]]));
$data = json_decode(file_get_contents($data_file), true);

// Proses beli
$message = '';
if(isset($_POST['buy'])){
    $idx = intval($_POST['idx']);
    if(isset($data['barang'][$idx])){
        if($data['barang'][$idx]['stok'] > 0){
            $data['barang'][$idx]['stok']--;
            file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
            $message = "Berhasil beli ".$data['barang'][$idx]['nama'];
        } else {
            $message = "Stok habis!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Toko Adrian</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; margin:0; padding:20px; }
h2 { text-align:center; color:#222; }
.container { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-top:20px; }
.card { background:#fff; padding:10px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); text-align:center; }
.card img { width:150px; height:150px; object-fit:cover; border-radius:8px; }
button { padding:8px 16px; border:none; border-radius:5px; background:#3b82f6; color:#fff; cursor:pointer; transition:0.3s; }
button:hover { background:#2563eb; }
.message { text-align:center; color:green; margin:10px 0; }
.logout { position:fixed; top:20px; right:20px; background:red; color:#fff; padding:8px 12px; border:none; border-radius:6px; cursor:pointer; }
</style>
</head>
<body>

<h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
<button class="logout" onclick="location.href='logout.php'">Logout</button>
<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>

<div class="container">
<?php foreach($data['barang'] as $i => $item): ?>
<div class="card">
    <img src="<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
    <div><strong><?php echo htmlspecialchars($item['nama']); ?></strong></div>
    <div>Stok: <?php echo intval($item['stok']); ?></div>
    <form method="post">
        <input type="hidden" name="idx" value="<?php echo $i; ?>">
        <button name="buy">Beli</button>
    </form>
</div>
<?php endforeach; ?>
</div>

</body>
</html>
