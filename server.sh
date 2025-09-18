#!/bin/bash
clear

banner() {
    echo -e "\e[1;92m============================================\e[0m"
    echo -e "\e[1;96m        Toko Adrian - Server Panel          \e[0m"
    echo -e "\e[1;92m============================================\e[0m"
}

menu() {
    echo ""
    echo -e "\e[1;33m[1] Jalankan Serveo + PHP Server\e[0m"
    echo -e "\e[1;33m[0] Keluar\e[0m"
    read -p "Pilih opsi: " option
}

start_php_serveo() {
    echo "[*] Membersihkan port 3333..."
    fuser -k 3333/tcp >/dev/null 2>&1

    echo "[*] Menjalankan PHP server di localhost:3333..."
    php -S localhost:3333 >/dev/null 2>&1 &
    sleep 2

    echo "[*] Membuat tunnel Serveo..."
    ssh -o StrictHostKeyChecking=no -R 80:localhost:3333 serveo.net
}

# Loop menu
while true; do
    clear
    banner
    menu
    case "$option" in
        1)
            start_php_serveo
            ;;
        0)
            echo "Keluar..."
            exit 0
            ;;
        *)
            echo "[!] Pilihan tidak valid."
            sleep 1
            ;;
    esac
done
