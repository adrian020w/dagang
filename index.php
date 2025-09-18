<?php
$data = json_decode(file_get_contents("data.json"), true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Toko Adrian</title>
</head>
<body>
<h1>Daftar Produk</h1>
<ul>
<?php foreach($data['produk'] as $p): ?>
    <li>
        <strong><?= $p['nama'] ?></strong> -
        Stok: <?= $p['stok'] ?> -
        Harga: Rp <?= number_format($p['harga']) ?>
    </li>
<?php endforeach; ?>
</ul>
<p>Diupdate terakhir: <?= date("Y-m-d H:i:s") ?></p>
</body>
</html>
