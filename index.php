<?php
$data = json_decode(file_get_contents('data.json'), true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Toko Adrian</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; margin:0; padding:20px; }
h1 { text-align:center; color:#333; }
.grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px; margin-top:20px; }
.card { background:white; border-radius:12px; padding:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center; }
.card img { max-width:100%; border-radius:8px; }
.nama { font-weight:bold; margin-top:10px; }
.stok { color:#555; margin-top:5px; }
</style>
</head>
<body>

<h1>Stok Barang Toko Adrian</h1>
<div class="grid">
<?php foreach($data['barang'] as $item): ?>
    <div class="card">
        <img src="<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama']; ?>">
        <div class="nama"><?php echo $item['nama']; ?></div>
        <div class="stok">Stok: <?php echo $item['stok']; ?></div>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
