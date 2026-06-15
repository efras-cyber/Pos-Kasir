# Perubahan Struk Printer Thermal

Tanggal perubahan: 14 Mei 2026

Dokumen ini menjelaskan perubahan pada fitur cetak struk transaksi agar mendukung ukuran kertas thermal 58mm dan 80mm. Catatan ini bisa dipakai sebagai lesson tambahan untuk memahami masalah awal, solusi yang dibuat, dan file yang berubah.

## Latar Belakang

Halaman detail transaksi sebelumnya hanya mencetak struk melalui `window.print()` dengan layout kertas 80mm. Saat dipakai pada printer thermal 58mm, hasil cetak menjadi tidak rapi: teks dan nominal di sisi kanan terpotong karena area layout lebih lebar daripada area cetak efektif printer.

Printer thermal 58mm biasanya tidak benar-benar punya area cetak 58mm penuh. Pada printer yang diuji, area cetak efektif lebih aman sekitar 48mm, sehingga konten struk perlu dibuat lebih sempit walaupun paper size di sistem tetap 58mm.

## Sebelum

| Area | Kondisi Sebelum |
| --- | --- |
| Ukuran struk | Hardcode ke 80mm. |
| Pengaturan toko | Belum ada opsi ukuran struk. |
| Halaman detail transaksi | Tidak ada pilihan 58mm atau 80mm sebelum cetak. |
| CSS print | `#print-area` memakai `width: 80mm` dan `@page size: 80mm auto`. |
| Hasil pada printer 58mm | Teks dan nominal sisi kanan kepotong/geser. |
| Modal print browser | Tetap muncul karena memakai `window.print()`. |

## Sesudah

| Area | Kondisi Sesudah |
| --- | --- |
| Ukuran struk | Mendukung opsi 58mm dan 80mm. |
| Default ukuran | Default 58mm jika belum ada setting tersimpan. |
| Pengaturan toko | Ada pilihan `Ukuran Struk` 58mm / 80mm. |
| Halaman detail transaksi | Ada toggle cepat `Ukuran Struk` sebelum tombol cetak. |
| CSS print 58mm | Paper size tetap 58mm, tetapi area konten dibuat 48mm supaya tidak terpotong. |
| CSS print 80mm | Area konten tetap 80mm dengan padding, logo, dan font yang lebih proporsional. |
| Tampilan teks | Berat font print diturunkan supaya hasil thermal tidak terlalu tebal. |
| Logo | Logo print diperbesar agar tidak terlihat terlalu kecil. |
| Cetak tanpa modal | Disarankan memakai Chrome kiosk printing, bukan service tambahan. |

## File Yang Berubah

### `app/Models/Setting.php`

Menambahkan key `receipt_paper_size` ke data `store` yang dibagikan ke frontend.

Perubahan utama:
- Membaca value `receipt.paper_size` dari tabel `settings`.
- Hanya menerima nilai `58` atau `80`.
- Jika setting belum ada atau tidak valid, default menjadi `58`.

### `app/Http/Controllers/Account/SettingController.php`

Menambahkan penyimpanan ukuran struk dari halaman pengaturan toko.

Perubahan utama:
- Validasi field `receipt_paper_size` dengan nilai `58` atau `80`.
- Menyimpan setting ke key `receipt.paper_size`.
- Field dibuat nullable dengan fallback `58` supaya update setting lama tetap aman.

### `resources/js/Pages/Account/Settings/Index.jsx`

Menambahkan pilihan ukuran struk pada halaman pengaturan toko.

Perubahan utama:
- State baru `receiptPaperSize`.
- Mengirim `receipt_paper_size` saat form setting disimpan.
- Menampilkan tombol pilihan `58mm` dan `80mm`.

### `resources/js/Pages/Account/Transactions/Show.jsx`

Menambahkan pilihan ukuran struk pada halaman detail transaksi dan mengatur `@page` print secara dinamis.

Perubahan utama:
- Menambahkan daftar ukuran `["58", "80"]`.
- Menambahkan fungsi normalisasi ukuran supaya input tetap valid.
- Menambahkan toggle `Ukuran Struk` di panel aksi transaksi.
- Menambahkan class `receipt-paper--58mm` atau `receipt-paper--80mm` pada area struk.
- Saat klik cetak, aplikasi membuat style `@page` sesuai pilihan:
  - 58mm memakai `size: 58mm auto`.
  - 80mm memakai `size: 80mm auto`.

### `public/assets/css/styles.css`

Menambahkan styling preview dan print untuk ukuran 58mm dan 80mm.

Perubahan utama:
- Menambahkan style tombol pilihan ukuran struk.
- Preview 58mm dibuat lebih sempit daripada 80mm.
- Print 58mm:
  - paper size 58mm;
  - area konten 48mm;
  - padding 1.5mm;
  - kolom meta/summary dibuat grid agar nominal tidak mepet kanan;
  - subtotal item diberi batas lebar agar tidak terpotong.
- Print 80mm:
  - area konten 80mm;
  - padding 4mm;
  - logo 18mm;
  - font lebih besar dari 58mm.
- Font print diturunkan dari bobot tebal berat ke bobot yang lebih aman untuk thermal printer.

## Cara Pakai Ukuran 58mm

Di aplikasi POS:
- Buka detail transaksi.
- Pilih ukuran struk `58mm`.
- Klik `Cetak Struk`.

Di dialog print macOS:
- Paper Size: `Thermal 58x200`.
- Width: `58 mm`.
- Height: `200 mm`.
- Margins: `0 mm`.
- Scaling: `100%`.
- Orientation: portrait.

Jika struk panjang dan bagian bawah terpotong, buat custom paper baru:
- `Thermal 58x300`, atau
- `Thermal 58x500`.

## Cara Pakai Ukuran 80mm

Di aplikasi POS:
- Buka detail transaksi.
- Pilih ukuran struk `80mm`.
- Klik `Cetak Struk`.

Di dialog print macOS:
- Paper Size: `Thermal 80x200`.
- Width: `80 mm`.
- Height: `200 mm`.
- Margins: `0 mm`.
- Scaling: `100%`.
- Orientation: portrait.

Jika struk panjang dan bagian bawah terpotong, buat custom paper baru:
- `Thermal 80x300`, atau
- `Thermal 80x500`.

## Catatan Penting

Fitur `Cetak Struk` masih memakai `window.print()`. Jika aplikasi dibuka dari browser biasa, modal/dialog print browser tetap muncul. Ini adalah batasan normal browser. Printer yang sudah terhubung tidak otomatis membuat browser bisa silent print.

Solusi paling sederhana untuk cetak tanpa modal adalah menjalankan Chrome khusus POS dengan `--kiosk-printing`. Chrome utama tidak perlu ditutup jika memakai profile terpisah:

```bash
open -na "Google Chrome" --args --user-data-dir="$HOME/.pos-chrome-print" --kiosk-printing --app="http://127.0.0.1:8000"
```

Ganti `http://127.0.0.1:8000` dengan URL aplikasi POS yang sedang dipakai.

Syarat agar kiosk print berjalan rapi:
- Printer thermal dijadikan default printer.
- Paper size custom sudah dibuat, misalnya `Thermal 58x200` atau `Thermal 80x200`.
- Scaling print terakhir diset `100%`.
- Aplikasi POS dibuka dari Chrome khusus kiosk tersebut.
- Tombol di aplikasi tetap memakai `Cetak Struk`.

## Verifikasi Yang Dilakukan

Perintah yang sudah dijalankan:

```bash
npm run build
php -l app/Models/Setting.php
php -l app/Http/Controllers/Account/SettingController.php
```

Hasil:
- Build frontend berhasil.
- Sintaks PHP file yang diubah valid.

Catatan test:
- `php artisan test` masih gagal pada test bawaan `Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response`.
- Penyebabnya route `/` mengembalikan status `302`, sedangkan test bawaan mengharapkan `200`.
- Kegagalan ini bukan berasal dari perubahan fitur struk.

## Ringkasan Hasil

Perubahan ini membuat project lebih fleksibel untuk printer thermal:
- 58mm sudah disesuaikan dengan printer thermal kecil dan sudah diuji lebih pas.
- 80mm sudah disiapkan dengan layout yang lebih lega dan proporsional.
- Pengaturan ukuran bisa disimpan dari halaman setting toko.
- Kasir juga bisa mengganti ukuran langsung dari halaman detail transaksi sebelum mencetak.
- Untuk cetak tanpa modal, jalankan POS melalui Chrome khusus kiosk printing.
