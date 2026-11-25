# Portal alternatif

## Bahasa antarmuka

Pemilih bahasa tersedia pada login/register, dashboard, dan topbar panel
portal maupun admin. Pilih **English**, **Indonesia**, atau **中文（简体）**,
lalu tekan **Apply / Terapkan / 应用**. Default mengikuti `APP_LOCALE`.
Preferensi disimpan di session dan cookie terenkripsi selama satu tahun,
termasuk setelah logout. Tidak diperlukan migrasi database.

Middleware `SetLocale` juga berjalan pada request Livewire. Terjemahan
portal dan label aplikasi berada di `lang/en.json`, `lang/id.json`, dan
`lang/zh_CN.json`; kontrol bawaan memakai katalog bahasa Filament.
Validasi utama Indonesia/Mandarin ada di direktori locale masing-masing.
Nama perusahaan, nama orang, catatan, dokumen PDF, serta data input pengguna
tidak diterjemahkan otomatis. String khusus yang belum tercantum di katalog
tetap memakai teks sumber. Tambahkan padanan pada ketiga katalog saat
menambahkan label baru.

`LocaleTest` menguji pilihan bahasa, cookie, validasi, kedua panel,
Companies, dan redirect lokal. `CompanyPanelParityTest` memastikan data
aktif/terhapus pada kedua panel tetap identik dengan filter yang sama.

Buka `/portal` atau tombol **Tampilan Portal** pada dashboard Filament.
Pengguna yang belum login diarahkan ke `/portal/login`; URL tujuan disimpan
di session. Hak akses panel dan modul mengikuti akun Filament yang sama.

Portal menggunakan Blade dan aset CSS/JavaScript terpisah, terinspirasi
tampilan TailAdmin (https://tailadmin.com). Ini bukan instalasi template
TailAdmin Pro. Tidak membutuhkan perubahan database.

Dashboard menampilkan jumlah dan delapan pengajuan terakhir milik pengguna
untuk modul approval yang dapat diaksesnya. Status menunggu dihitung dari
`Submitted`, sesuai service dashboard yang sudah ada. Cakupan seluruh periode.
Menu layanan dan tautan tabel membuka area `/portal/workspace`. Seluruh
resource dan halaman memakai kembali komponen dan aturan bisnis Filament
dengan tema portal tersendiri. Filament lama tetap tersedia di `/admin`.

## Navigasi lengkap

Sidebar dan direktori **Semua menu** menggunakan `PortalNavigationService`
yang membaca navigation builder panel portal Filament, bukan daftar modul
approval. Label, ikon, urutan, grup, badge, child items, visibility dan policy
tetap mengikuti konfigurasi Filament. Menu baru yang didaftarkan ke navigasi
Filament otomatis tersedia untuk akun yang berhak. Dashboard diarahkan ke
`/portal`; menu fitur lainnya memakai URL `/portal/workspace/...`.

Grup sidebar dapat dibuka-tutup. Pencarian direktori mencocokkan nama menu
dan grup. Ringkasan angka tetap khusus pengajuan pribadi, terpisah dari
daftar menu lengkap. Tes `PortalNavigationTest` memeriksa menu karyawan,
menu superadmin, serta rendering endpoint portal untuk kedua peran.

Mode gelap disimpan di localStorage dengan kunci `portal-theme`. Pencarian
layanan bekerja di browser. Aset berada di `public/css/portal.css` dan
`public/js/portal.js`. Halaman isi memakai tema Vite tersendiri.

## Halaman isi menu

`PortalPanelProvider` mendaftarkan resource dan halaman yang sama dengan
panel admin. Tabel, pencarian, filter, pagination, create/edit, validasi,
approval, lampiran, tanda tangan, profil, serta laporan memakai komponen
Livewire yang sudah ada. Tidak ada iframe atau duplikasi proses bisnis.
Tautan edit reimbursement dan payment application mengikuti panel aktif.
Route PDF/Excel dan dokumen privat tetap memakai endpoint bersama.

Tema berada di `resources/css/filament/portal/theme.css` dan hanya dimuat
oleh panel portal. Jalankan `npm ci` lalu `npm run build` saat deployment;
hasilnya berada di `public/build`. Tema admin lama tidak diganti.
Autentikasi area kerja menggunakan sesi web dan izin panel yang sama;
pengguna tanpa login diarahkan ke login portal.

`PortalWorkspaceTest` memeriksa setiap menu terlihat dan formulir create
yang diizinkan untuk lima profil peran, kesetaraan route resource, penolakan
akses, serta formulir create/edit dan pencarian tabel melalui Livewire.

Validasi: `php vendor/phpunit/phpunit/phpunit --filter 'PortalTest|ExampleTest'`.
Tes mencakup redirect login, penolakan akun tanpa akses panel, rendering
kosong/berisi, escaping konten, serta route halaman utama yang tetap.
Tampilan visual perlu dicek di browser.

## Login dan pendaftaran

`/portal/login` dan `/portal/register` menggunakan tampilan dua kolom bergaya
TailAdmin dengan mode gelap, formulir responsif, dan tombol visibilitas sandi.
Login menggunakan guard web yang sama dengan Filament, mendukung remember-me,
dan meregenerasi sesi. Logout menggunakan POST dengan CSRF dan menghapus sesi.
Endpoint login dan register dibatasi lima permintaan per menit per IP.

Pendaftaran dapat dinonaktifkan dengan `PORTAL_REGISTRATION_ENABLED=false`.
Default tanpa konfigurasi adalah nonaktif di production; `.env.example`
juga menonaktifkannya secara eksplisit. Administrator awal dapat dibuat
melalui `php artisan app:create-administrator` tanpa kredensial bawaan.

Pendaftaran membuat akun `User` saja, mengabaikan field jabatan/peran dari
request. Email harus unik dan sandi minimal delapan karakter termasuk huruf
dan angka, dengan konfirmasi. Hash menggunakan cast password model User.
Departemen dan profil karyawan tetap perlu dilengkapi administrator.
Pendaftaran tidak mengirim email verifikasi atau menyediakan reset sandi.
Login Filament tetap tersedia di `/admin/login`.

Tes autentikasi: `php vendor/phpunit/phpunit/phpunit --filter PortalAuthTest`.
Tes database memakai transaksi rollback pada `insys_db_testing`.
