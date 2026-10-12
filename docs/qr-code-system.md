# QR-Code System — Rencana & Konsep

> **File acuan tunggal.** Semua konsep, keputusan, struktur, dan desain disimpan HANYA di file ini.
> Jangan buat file rencana lain agar tidak melebar. Setiap perubahan konsep/desain diupdate di sini.

---

## 1. Ringkasan Proyek

| Aspek | Keputusan |
|---|---|
| Nama proyek | **QR-Code System** (folder proyek: `qr-code-system`) |
| Tujuan | Sistem absensi dengan scan QR code |
| Pengguna | Siswa/Mahasiswa (+ admin/pengajar) |
| Stack | **Laravel (PHP) + MySQL/MariaDB** |
| Frontend | Blade + Tailwind CSS (**default, belum dipoles**) |
| Model QR | **QR kelas per sesi**, token unik (bukan ID polos) |
| Alur scan | **Siswa login di HP sendiri → scan QR kelas** yang ditempel di kelas |
| Lokasi scan | **Self-service**, tidak pakai kiosk/petugas |
| Jadwal | **Berulang otomatis** (generate sesi dari pola) |
| Status kehadiran | `hadir`, `telat`, `izin`, `sakit`, + `alpha` (dihitung) |
| Validasi lokasi | **Tidak perlu** (tanpa GPS/geofence) |
| Kelola data | **Import CSV/Excel + input manual** |
| Export | **Excel + PDF** |
| Hosting | **Belum ada — uji coba lokal (localhost)** dulu |
| UI/UX | **Default dulu**; konsep desain dibahas setelah fungsional beres |

---

## 2. Keputusan Kunci & Catatan

1. **QR yang di-scan adalah QR KELAS (milik sesi), bukan QR pribadi siswa.**
   Karena siswa men-scan QR yang ditempel di kelas pakai HP sendiri.
2. **Identitas absensi diambil dari akun login siswa**, bukan dari isi QR.
   → Inilah dasar anti titip absen (QR kelas tidak berisi data siswa).
3. **Jangan isi QR dengan `student_id` polos.** QR berisi `qr_token` acak 64 hex char:
   `bin2hex(random_bytes(32))`.
4. **Anti duplikat** dijaga dua lapis: constraint DB `unique(student_id, session_id)` + cek query.
5. **`alpha` tidak disimpan** → dihitung dinamis (`total sesi − (hadir+telat+izin+sakit)`).
6. **Jadwal berulang otomatis** via command artisan + Laravel scheduler → generate `schedule_sessions`.
7. **Token QR per sesi** bisa di-regenerate (bonus keamanan).

### Catatan Kamera di Browser
- Kamera browser hanya jalan di **`https`** atau **`localhost`**.
- Dev lokal `php artisan serve` (http://localhost:8000) → **aman, kamera jalan**.
- Kalau tes dari HP di jaringan lokal via IP (`192.168.x.x`) → kamera **diblokir**.
  Solusi saat itu: `ngrok` / tunnel HTTPS, atau `mkcert`/`valet` untuk SSL lokal. (Catat, tak menghambat sekarang.)

---

## 3. Struktur Database

### `users` (autentikasi)
```
id          bigint PK
name        varchar
email       varchar unique
password    varchar
role        enum('admin','siswa') default 'siswa'
created_at, updated_at
```

### `students` (profil akademik)
```
id          bigint PK
user_id     bigint FK -> users.id (cascade)
nis         varchar unique      -- NIS/NIM
nama        varchar
phone       varchar nullable
created_at, updated_at
```

### `classes` (kelas / mata pelajaran)
```
id          bigint PK
nama        varchar
kode        varchar unique
guru_id     bigint FK -> users.id nullable
created_at, updated_at
```

### `class_student` (pivot: siswa terdaftar di kelas)
```
id          bigint PK
class_id    bigint FK -> classes.id (cascade)
student_id  bigint FK -> students.id (cascade)
unique(class_id, student_id)
```

### `schedules` (pola/master jadwal berulang)
```
id             bigint PK
class_id       bigint FK -> classes.id (cascade)
hari           enum('senin','selasa','rabu','kamis','jumat','sabtu')
jam_mulai      time
jam_selesai    time
toleransi      int default 15          -- menit toleransi telat
tanggal_mulai  date                    -- rentang berlaku pola
tanggal_selesai date
created_at, updated_at
```

### `schedule_sessions` (sesi konkret hasil generate)
```
id            bigint PK
schedule_id   bigint FK -> schedules.id (cascade)
class_id      bigint FK -> classes.id (cascade)
tanggal       date
jam_mulai     time
jam_selesai   time
toleransi     int
qr_token      char(64) unique          -- token QR kelas utk sesi ini
qr_token_aktif boolean default true
created_at, updated_at
unique(class_id, tanggal, jam_mulai)   -- anti sesi ganda
```

### `attendances` (hasil scan)
```
id            bigint PK
student_id    bigint FK -> students.id
session_id    bigint FK -> schedule_sessions.id
waktu_scan    datetime                 -- waktu server
status        enum('hadir','telat','izin','sakit')
ip_address    varchar nullable
user_agent    varchar nullable
created_at, updated_at
unique(student_id, session_id)         -- KUNCI anti duplikat
```

### (Opsional, belum dipakai) `student_qr_tokens`
> Disimpan sebagai cadangan bila nanti mau QR pribadi siswa.
```
id          bigint PK
student_id  bigint FK -> students.id (cascade)
token       char(64) unique
aktif       boolean default true
created_at, updated_at
```

---

## 4. Relasi (ERD ringkas)

```
users(login) ─1:1─ students ──┬── class_student ── classes
                              │                       │
                              │                    schedules (pola)
                              │                       │ generate
                              │                       ▼
                              └───────────────► schedule_sessions (qr_token)
                                                      │
                            scan(login siswa) ────────▼
                                              attendances (unique student+session)
                                                      │
                                              rekap & tracking (alpha dihitung)
```

---

## 5. Teknis Detail

### 5.1 Generate jadwal berulang
- Admin isi **pola** di `schedules` (hari + jam + toleransi + rentang tanggal).
- Command `schedule:generate` dijalankan harian via Laravel Scheduler:
  - Buat baris `schedule_sessions` untuk tanggal yang cocok pola.
  - Generate `qr_token = bin2hex(random_bytes(32))` tiap sesi.

### 5.2 Alur scan siswa (self-service)
1. Siswa login → buka halaman scan.
2. Kamera baca **QR kelas** → ambil `qr_token`.
3. Kirim ke endpoint `POST /scan` (token + sesi auth user).
4. Backend validasi:
   - **Token cocok**: ada `schedule_sessions` dengan token tsb (`qr_token_aktif = 1`).
   - **Terdaftar**: siswa ada di `class_student` untuk kelas sesi itu. Jika tidak → tolak.
   - **Window waktu**:
     - `sekarang < jam_mulai − toleransi` → tolak "belum waktunya".
     - `jam_mulai − toleransi ≤ sekarang ≤ jam_selesai` → boleh.
     - `sekarang > jam_selesai` → tolak "kelas sudah selesai".
   - **Status**: `sekarang ≤ jam_mulai + toleransi` → `hadir`; else → `telat`.
   - **Anti duplikat**: `unique(student_id, session_id)` → tolak "sudah absen".
5. Simpan + feedback ke siswa.

### 5.3 Anti titip absen (desain)
- Identitas dari **akun login** (QR kelas tak memuat data siswa).
- 1 akun = 1 absen per sesi (constraint DB).
- Rate limit endpoint scan: `throttle:30,1`.
- Token QR per sesi non-tebakan (32 byte acak) + bisa di-regenerate.

### 5.4 Import & Export
- Import siswa/kelas: **`maatwebsite/excel`** (CSV/Excel), validasi baris (NIS duplikat dsb).
- Export Excel: **`maatwebsite/excel`**.
- Export PDF: **`barryvdh/laravel-dompdf`**.
- Dipakai di rekap (per siswa & per kelas/jadwal).

### 5.5 Rekap / tracking kehadiran
```php
$totalSesi = ScheduleSession::where('class_id', $classId)->count();
$hadir = Attendance::where('student_id',$id)->where('status','hadir')->count();
$telat = Attendance::where(...)->where('status','telat')->count();
$izin  = Attendance::where(...)->where('status','izin')->count();
$sakit = Attendance::where(...)->where('status','sakit')->count();
$alpha = $totalSesi - ($hadir + $telat + $izin + $sakit);
$persenHadir = $totalSesi ? round(($hadir + $telat) / $totalSesi * 100) : 0;
```
- Filter per kelas / tanggal / siswa. Query agregat untuk dashboard.

### 5.6 Keamanan
- Middleware role (admin ≠ siswa).
- CSRF di form scan. HTTPS wajib untuk kamera.
- Rate limiting endpoint scan.
- Audit: simpan `ip_address` + `user_agent` di `attendances`.

---

## 6. Roadmap Implementasi

1. Setup proyek + `.env` + MySQL (`composer create-project laravel/laravel qr-code-system`).
2. Migrasi semua tabel + seeder data dummy.
3. Auth + role (Laravel Breeze).
4. Admin: CRUD kelas, siswa, + import CSV/Excel.
5. Pola jadwal + command generate sesi + QR token.
6. Halaman scan siswa (`html5-qrcode`) + endpoint validasi.
7. Dashboard/rekap siswa & admin + export (Excel + PDF).
8. Testing lokal.
9. (Menyusul) Konsep & desain UI/UX.
10. (Menyusul) Deploy + HTTPS.

---

## 7. Status UI/UX

- **Saat ini:** default Blade + Tailwind, belum dipoles.
- **Rencana:** setelah semua fitur fungsional stabil, saya akan menyusun **beberapa opsi konsep desain** (mis. *clean academic*, *dashboard-first*, *minimal kiosk-style*) beserta referensi, lalu user memilih.
- Keputusan desain final **dicatat di file ini**.

---

## 8. Belum Diputuskan (tidak menghambat)

- Nama domain & port (nanti saat deploy).
- Sumber hosting / VPS.
- Konsep UI/UX final.
- Format spesifik file import/export.

---

## 9. Riwayat Keputusan

- QR statis per orang → **berubah** jadi **QR kelas per sesi** (karena alur: siswa scan QR kelas pakai HP sendiri).
- GPS/geofence → **tidak perlu**.
- `alpha` → dihitung dinamis, tidak disimpan.
- Hosting → tunda, uji lokal dulu.
- Export → Excel + PDF.
- Laravel versi → **13.35.0** (terbaru saat setup).

---

## 10. Progres Implementasi

### Lingkungan
- PHP 8.5, Composer 2.9, MariaDB 12.2, Node 26 (Vite 8 + Tailwind 4).
- Laravel **13.35.0**, Laravel Boost terinstall (MCP + guidelines).
- **Lokasi proyek:** `/home/kal/Documents/Coding Vibe/Qr-code`
- **Dokumen ini:** `docs/qr-code-system.md` (file acuan tunggal).

### Database
- `.env` → connection `mysql`, database `qr_code_system`, user `root`.
- **Semua migrasi selesai & terpasang (9 tabel):**
  `users` (+kolom role), `students`, `classes`, `class_student`,
  `schedules`, `schedule_sessions`, `attendances`, `cache`, `jobs`.
- **Model + relasi + factory + seeder selesai.**
  - Model: `User` (role, isAdmin/isSiswa), `Student`, `ClassRoom` (tabel `classes`),
    `Schedule`, `ScheduleSession`, `Attendance` — lengkap dengan relasi.
  - Factory semua model + state `admin()`/`siswa()` di `UserFactory`.
  - `DatabaseSeeder`: 1 admin, guru+pola jadwal+sesi+absensi dummy,
    + akun siswa demo (`siswa@qrcode.test` / `password`), admin (`admin@qrcode.test` / `password`).

### Akun Demo (hasil seeding)
| Role | Email | Password |
|---|---|---|
| Admin | admin@qrcode.test | password |
| Siswa | siswa@qrcode.test | password |

### Checklist Roadmap
- [x] Setup proyek + `.env` + MySQL
- [x] Migrasi semua tabel
- [x] Model + relasi + factory + seeder
- [x] Auth + role (Breeze)
- [x] Admin: CRUD kelas
- [x] Admin: CRUD siswa (auto-generate akun, password = NIS)
- [x] Admin: import siswa CSV/Excel (kolom `kode_kelas`)
- [ ] Pola jadwal + command generate sesi + QR token
- [ ] Halaman scan siswa + endpoint validasi
- [ ] Dashboard/rekap + export Excel & PDF
- [ ] Testing lokal
- [ ] Konsep & desain UI/UX
- [ ] Deploy + HTTPS

### Admin CRUD (selesai)
- **Kelas** (`/admin/classes`): CRUD lengkap, validasi kode unik, opsi guru dari user admin.
- **Siswa** (`/admin/students`): CRUD lengkap.
  - Saat tambah siswa → otomatis buat akun `users` (role=siswa) + `students`.
  - **Password awal = NIS**.
  - Pendaftaran kelas via checkbox di form siswa (`class_student` sync).
  - Hapus siswa ikut menghapus akun login-nya.
- Route: `admin.classes.*`, `admin.students.*`.
- Test: `ClassRoomTest` (6), `StudentTest` (6). Total suite 41 test.

### Catatan Operasional
- **Jangan `pkill` server dev** (`artisan serve`) — bisa meninggalkan lock tabel `sessions`
  di MariaDB & membuat test hang. Hentikan dengan Ctrl+C.

### Import Siswa (selesai)
- Library: **`maatwebsite/excel` v4** (butuh `ext-gd` + `ext-iconv`, sudah di-enable di php.ini).
- Route: `admin.students.import.create|store|template`.
- File: `StudentsImport` (ToCollection + WithHeadingRow + WithValidation + SkipsOnFailure),
  `StudentTemplateExport` (template .xlsx), `StudentImportController`.
- Kolom: `nis`(wajib, unik), `nama`(wajib), `email`(wajib, unik), `phone`(opsi), `kode_kelas`(opsi).
- **`kode_kelas`** → otomatis daftarkan siswa ke kelas jika kode cocok.
- Password awal = NIS. Import **parsial**: baris valid tetap masuk, baris gagal dilaporkan.
- Halaman import menampilkan error per baris + tombol unduh template.

### Auth & Role (selesai)
- **Laravel Breeze** (stack Blade) terinstall.
- **Register publik dihapus** → akun siswa hanya dibuat admin.
- **Middleware `role`** (`EnsureUserHasRole`) — alias `role`, dipakai:
  - `/dashboard` → `role:siswa`
  - `/admin/dashboard` → `role:admin`
- Redirect setelah login menyesuaikan role:
  - siswa → `dashboard`
  - admin → `admin.dashboard`
- Navigation Blade menyesuaikan menu berdasarkan role.
- View: `student/dashboard.blade.php`, `admin/dashboard.blade.php`.

### Lingkungan Testing
- `pdo_sqlite` tidak tersedia di sistem → test pakai **MySQL** DB terpisah: `qr_code_system_test`.
- Konfigurasi di `phpunit.xml`.
- **29 test lolos**, termasuk `RoleAccessTest` & `AuthenticationTest` (redirect per role).
