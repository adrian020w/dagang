<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$data = json_decode(file_get_contents('data.json'), true);

if(isset($_POST['buy'])){
    $idx = $_POST['idx'];
    if($data['barang'][$idx]['stok']>0){
        $data['barang'][$idx]['stok']--;
        file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));
        $message = "Berhasil beli ".$data['barang'][$idx]['nama'];
    } else {
        $message = "Stok habis!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Toko Adrian</title>
</head>
<body>
<h2>Selamat datang, <?php echo $_SESSION['user']; ?></h2>
<?php if(isset($message)) echo "<p>$message</p>"; ?>
<?php foreach($data['barang'] as $i=>$item): ?>
<div>
    <img src="<?php echo $item['gambar']; ?>" width="150"><br>
    <?php echo $item['nama']; ?> - Stok: <?php echo $item['stok']; ?><br>
    <form method="post">
        <input type="hidden" name="idx" value="<?php echo $i; ?>">
        <button name="buy">Beli</button>
    </form>
</div>
<hr>
<?php endforeach; ?>
</body>
</html>
