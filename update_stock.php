<?php
if (php_sapi_name() !== 'cli') exit('Hanya bisa dari terminal');
$data = json_decode(file_get_contents("data.json"), true);

// Contoh: update stok dari terminal
$produk_index = $argv[1] ?? 0;
$new_stok = $argv[2] ?? 0;

$data['produk'][$produk_index]['stok'] = intval($new_stok);

file_put_contents("data.json", json_encode($data, JSON_PRETTY_PRINT));
echo "Stok berhasil diperbarui.\n";
