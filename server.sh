#!/bin/bash

DATA_FILE="data.json"
PORT=3333

trap ctrl_c INT

ctrl_c() {
    echo -e "\n[!] Ctrl+C terdeteksi. Membersihkan proses..."
    pkill -f php >/dev/null 2>&1
    pkill -f ssh >/dev/null 2>&1
    exit 0
}

banner() {
    clear
    echo "==============================="
    echo "      TOKO ADRIAN ADMIN"
    echo "==============================="
    echo
}

start_php() {
    fuser -k $PORT/tcp >/dev/null 2>&1
    php -S localhost:$PORT >/dev/null 2>&1 &
    sleep 2
}

start_serveo() {
    echo "[*] Memulai Serveo..."
    [[ -f sendlink ]] && rm sendlink
    ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -R 80:localhost:$PORT serveo.net 2>/dev/null > sendlink &
    sleep 8
    LINK=$(grep -o "https://[0-9a-z]*\.serveo.net" sendlink)
    echo "[+] Serveo link: $LINK"
}

show_menu() {
    echo
    echo "=== MENU ADMIN ==="
    echo "[1] Tampilkan barang"
    echo "[2] Update stok/nama/gambar"
    echo "[0] Keluar"
    echo
}

display_items() {
    echo
    jq -r '.barang[] | "\(.nama) | Stok: \(.stok) | Gambar: \(.gambar)"' "$DATA_FILE"
    echo
}

update_item() {
    echo
    jq -r '.barang | to_entries[] | "\(.key): \(.value.nama)"' "$DATA_FILE"
    read -p "Pilih nomor barang untuk diubah: " idx
    ITEM=$(jq -r ".barang[$idx]" "$DATA_FILE")
    if [ "$ITEM" == "null" ]; then
        echo "[!] Index tidak valid"
        return
    fi
    read -p "Nama baru (biarkan kosong jika tidak diubah): " NAMA
    read -p "Stok baru (biarkan kosong jika tidak diubah): " STOK
    read -p "URL gambar baru (biarkan kosong jika tidak diubah): " GAMBAR

    [ -n "$NAMA" ] && jq ".barang[$idx].nama=\"$NAMA\"" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    [ -n "$STOK" ] && jq ".barang[$idx].stok=$STOK" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    [ -n "$GAMBAR" ] && jq ".barang[$idx].gambar=\"$GAMBAR\"" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    echo "[*] Barang berhasil diupdate!"
}

# Jalankan
banner
start_php
start_serveo

while true; do
    show_menu
    read -p "Pilihan: " OPT
    case $OPT in
        1) display_items ;;
        2) update_item ;;
        0) echo "Keluar..."; ctrl_c ;;
        *) echo "[!] Pilihan tidak valid" ;;
    esac
done
