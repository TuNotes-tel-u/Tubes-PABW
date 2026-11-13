# TuNotes — Dokumentasi API Notes & Perbaikan

Dokumen ini menjelaskan hasil analisis, perbaikan bug, kontrak REST API `notes`,
dan alur UI (Dashboard, Upload Notes, My Notes, Detail My Notes, Update Notes).

---

## 1. Bug yang ditemukan dan diperbaiki

| # | Masalah | Dampak | Perbaikan |
|---|---------|--------|-----------|
| 1 | `bootstrap/app.php` memiliki blok `->withExceptions(...)` duplikat setelah `->create()` | **Parse error**, seluruh aplikasi tidak bisa boot | Blok duplikat dihapus dan exception handler 404 JSON dirapikan |
| 2 | `routes/api.php` memakai `NotesController` yang tidak ada dan mendaftarkan route dua kali | Semua route API gagal dimuat | Diganti menjadi satu `Route::apiResource('notes', NoteController::class)` |
| 3 | `NoteController` memakai kolom yang tidak ada: `content`, `category_id`, `price`, `stock` | Validasi & insert gagal | Validasi disesuaikan dengan skema `notes` yang sebenarnya |
| 4 | `Response` tidak di-import pada method `destroy()` | Fatal error saat DELETE | Import `Illuminate\Http\Response` |
| 5 | Migrasi `notes` mereferensikan tabel `courses` yang belum ada | Migrasi gagal | Migrasi `courses` ditambahkan; migrasi dibuat idempotent karena tabel sudah ada di DB lama |
| 6 | Middleware `auth.notes` dipakai tetapi tidak terdaftar | Halaman dashboard error | Route web memakai middleware `auth` bawaan Laravel |
| 7 | `foreignId('user_id')` (BIGINT) tidak cocok dengan `users.id` (INT UNSIGNED) | Migrasi `categories` gagal | Kolom FK memakai `unsignedInteger('user_id')` |
| 8 | View Upload/My Notes/Detail/Update dan file JS tidak ada di proyek | CRUD tidak bisa dipakai dari UI | View Blade dibuat dari HTML/CSS yang diberikan + JS pemanggil API |
| 9 | Error 404 API berpotensi berupa halaman HTML | Melanggar spesifikasi JSON | `ForceJsonResponse` + render `NotFoundHttpException` khusus `api/*` |

---

## 2. Struktur data

Tabel utama: `notes` (sudah sesuai spesifikasi soal).

Kolom penting:

- `user_id` — pemilik catatan
- `title` — nullable; jika kosong, response memakai `display_title` = `file_name`
- `category` — nama kategori (default `TuNotes`)
- `tags` — JSON array
- `file_path`, `file_name`, `file_size`, `file_type` — metadata file
- `is_public`, `status` (`active`/`deleted`), statistik rating/download/view

Tabel pendukung yang ditambahkan:

- `courses` — target foreign key `notes.course_id`
- `categories` — kategori buatan user (kategori default tidak disimpan, berasal dari konstanta `Note::DEFAULT_CATEGORIES`)

Kategori default: `TuNotes`, `Kuliah`, `Tugas`, `Ringkasan`, `Lainnya`.

---

## 3. Endpoint API

Base URL: `http://localhost:8000/api`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/notes` | `notes.index` | Daftar notes aktif + filter |
| POST | `/notes` | `notes.store` | Tambah note, sukses `201` |
| GET | `/notes/{id}` | `notes.show` | Detail note, `404` JSON jika tidak ada |
| PUT/PATCH | `/notes/{id}` | `notes.update` | Update penuh/parsial, sukses `200` |
| DELETE | `/notes/{id}` | `notes.destroy` | Hapus permanen, sukses `204` tanpa body |

Query string `GET /notes`:

- `?category=Kuliah` — filter kategori
- `?search=sorting` — LIKE pada `title`, `file_name`, `course_name`, `lecturer_name`
- `?mine=1&user_id=1` — hanya notes milik user (atau memakai user login)
- `?is_public=1` — filter status publik
- `?sort=newest|popular|rating` — urutan

### 3.1 GET /api/notes

```bash
curl -H "Accept: application/json" \
  "http://localhost:8000/api/notes?category=Kuliah&search=sorting"
```

Response `200`:

```json
{
  "status": "success",
  "message": "Daftar catatan berhasil diambil",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "title": "Sorting",
      "display_title": "Sorting",
      "course_name": "Algoritma",
      "category": "Kuliah",
      "tags": ["sorting", "algoritma"],
      "file_type": "pdf",
      "is_public": true
    }
  ]
}
```

### 3.2 GET /api/notes/{id}

```bash
curl -H "Accept: application/json" http://localhost:8000/api/notes/999
```

Response `404` (JSON, bukan HTML):

```json
{
  "status": "error",
  "message": "Catatan tidak ditemukan."
}
```

### 3.3 POST /api/notes

Dua bentuk request yang didukung:

**a. JSON** (file sudah tersimpan):

```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Sorting",
    "course_name": "Algoritma",
    "lecturer_name": "Dr. Ahmad",
    "category": "Kuliah",
    "tags": ["sorting", "algoritma"],
    "description": "Ringkasan sorting",
    "file_path": "/uploads/notes/sample.pdf",
    "file_name": "sample.pdf",
    "file_size": 1024,
    "file_type": "pdf"
  }'
```

**b. multipart/form-data** (dipakai halaman Upload Notes):

```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Accept: application/json" \
  -F "course_name=Algoritma" \
  -F "category=Kuliah" \
  -F "file=@catatan.pdf"
```

Aturan validasi utama:

| Field | Aturan |
|-------|--------|
| `course_name` | wajib, string, maks 120 |
| `title` | opsional, string, maks 255 |
| `lecturer_name` | opsional, string, maks 120 |
| `category` | opsional, string, maks 50 |
| `tags` | opsional, array atau string dipisah koma |
| `description` | opsional, string |
| `is_public` | opsional, boolean |
| `page_count` | opsional, integer >= 1 |
| `file` | opsional, pdf/doc/docx/txt/ppt/pptx, maks 10MB |
| `file_path` | wajib jika tidak ada `file` |
| `file_name` | wajib jika tidak ada `file` |
| `file_size` | wajib jika tidak ada `file` |
| `file_type` | wajib jika tidak ada `file`, salah satu tipe di atas |

Validasi gagal → `422`:

```json
{
  "message": "The course name field is required. (and 4 more errors)",
  "errors": {
    "course_name": ["The course name field is required."],
    "file_path": ["The file path field is required when file is not present."]
  }
}
```

Sukses → `201` + header `Location`:

```json
{ "data": { "id": 1, "display_title": "Sorting", "...": "..." } }
```

### 3.4 PUT/PATCH /api/notes/{id}

Semua field memakai `sometimes`, sehingga PATCH parsial tidak mengosongkan field lain.

```bash
curl -X PATCH http://localhost:8000/api/notes/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"title":"Sorting Versi Baru"}'
```

Response `200` berisi data terbaru pada `data`.

### 3.5 DELETE /api/notes/{id}

```bash
curl -X DELETE http://localhost:8000/api/notes/1 \
  -H "Accept: application/json" -i
```

Response `204 No Content` tanpa body. Baris dihapus permanen (hard delete).

---

## 4. Alur UI

### Dashboard (`/dashboard`)

- Menampilkan daftar catatan publik aktif.
- Pencarian memakai parameter `query` dan sorting memakai `sort`.
- Kartu catatan mengarah ke halaman kategori.

### My Notes (`/notes`)

- Menampilkan catatan milik user yang login.
- Menampilkan chips kategori (default + kategori buatan user + kategori pada notes).
- Form sederhana **Tambah category baru** (POST `/notes/categories`).
- Klik kategori → halaman Detail My Notes.
- Pencarian di halaman ini memfilter kartu secara client-side.

### Upload Notes (`/notes/upload`)

- Form mengirim `POST /api/notes` sebagai `multipart/form-data`.
- Dropdown Category berisi kategori default + kategori user.
- Opsi **+ Tambah category baru…** menampilkan input teks kategori baru.
- File diunggah ke `public/uploads/notes/` dan metadata disimpan ke tabel `notes`.

### Detail My Notes (`/notes/category/{category}`)

- Menampilkan tabel catatan pada kategori tersebut.
- Tombol **Update** → halaman Update Notes.
- Tombol **Delete** → `DELETE /api/notes/{id}`, baris langsung dihapus dari tabel.
- Pencarian memfilter baris tabel secara client-side.

### Update Notes (`/notes/{id}/edit`)

- Form terisi data lama.
- Mengirim `POST /api/notes/{id}` dengan `_method=PATCH` (FormData) agar file opsional tetap bisa diunggah.
- Hanya field yang diubah yang dikirim; field lain tetap.

---

## 5. Catatan autentikasi API

- API tetap dapat diuji dengan `curl` tanpa login, sesuai contoh soal.
- Untuk request dari browser, `statefulApi()` (Sanctum) diaktifkan sehingga cookie session login ikut terpakai.
- `store()` menentukan `user_id` dari: user login → `user_id` pada request → user pertama di database.
- Jika tidak ada user sama sekali, response `422` dengan pesan yang jelas.

---

## 6. Menjalankan proyek

Proyek membutuhkan **PHP >= 8.3** (Composer menolak PHP 8.2).

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Database default proyek ini adalah MySQL `tunotes_db` (lihat `.env`).
Jika memakai SQLite, sesuaikan `DB_CONNECTION` terlebih dahulu.

Menjalankan pengujian:

```bash
php artisan test --filter=NoteApiTest
php artisan test --filter=NotePageTest
```

Hasil yang sudah diverifikasi:

- `NoteApiTest` — 7 test, 33 assertion, semua lulus.
- `NotePageTest` — 2 test, 12 assertion, semua lulus.
- `php artisan view:cache` — semua Blade berhasil dikompilasi.
