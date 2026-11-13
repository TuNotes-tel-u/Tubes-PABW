<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>Update Notes - TuNotes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/UpdateNotes.css') }}">
</head>
<body>

    <header class="app-header">
        <div class="header-container">
            <div class="header-left">
                <a href="{{ route('notes.category', $note->category ?: 'TuNotes') }}" class="back-button">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="header-title">Update Notes</h1>
            </div>

            <div class="header-right">
                <div class="notif-icons">
                    <button class="icon-btn">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge" data-notif-badge>3</span>
                    </button>
                    <button class="icon-btn">
                        <i class="fa-regular fa-envelope"></i>
                        <span class="badge" data-message-badge>2</span>
                    </button>
                </div>
                <div class="user-profile">
                    <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png') }}" alt="" class="avatar" data-user-avatar>
                    <span class="user-name" data-user-name>{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="page-intro">
            <h2 class="title">Update Notes</h2>
            <p class="subtitle">Edit uploaded lecture notes.</p>
        </div>

        <section class="form-card">
            <form id="updateForm" class="update-form" novalidate
                  data-note-id="{{ $note->id }}"
                  data-cancel-url="{{ route('notes.category', $note->category ?: 'TuNotes') }}">

                <div class="form-group">
                    <label for="courseName">Course Name</label>
                    <input type="text" id="courseName" name="course_name" value="{{ $note->course_name }}" placeholder="Masukkan nama mata kuliah">
                </div>

                <div class="form-group">
                    <label for="lecturerName">Lecturer Name</label>
                    <input type="text" id="lecturerName" name="lecturer_name" value="{{ $note->lecturer_name }}" placeholder="Masukkan nama dosen">
                </div>

                <div class="form-group">
                    <label for="tagsInput">Tags</label>
                    <input type="text" id="tagsInput" name="tags" value="{{ implode(', ', $note->tags ?? []) }}" placeholder="Pisahkan dengan koma, contoh: algoritma, data structure"
                           style="padding:0.6rem 0.9rem;border:1px solid #d1d5db;border-radius:6px;width:100%;font:inherit;">
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category"
                            style="padding:0.6rem 0.9rem;border:1px solid #d1d5db;border-radius:6px;width:100%;font:inherit;">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ $note->category === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Update File (Optional)</label>
                    <div class="upload-zone">
                        <i class="fa-solid fa-cloud-arrow-up cloud-icon"></i>
                        <p class="upload-text">Drop your file here or browse</p>
                        <p class="upload-hint">Supported formats: PDF, DOC, DOCX, TXT, PPT, PPTX (Max 10MB)</p>

                        <div class="file-preview">
                            <div class="file-info">
                                <i class="fa-solid fa-file-pdf pdf-icon"></i>
                                <span class="file-name" id="currentFileName">{{ $note->file_name }}</span>
                            </div>
                            <span class="file-size" id="currentFileSize">{{ number_format($note->file_size / 1024, 0) }} KB</span>
                        </div>

                        <input type="file" id="fileInputUpdate" name="file" style="display:none"
                               accept=".pdf,.doc,.docx,.txt,.ppt,.pptx">
                        <button type="button" id="chooseNewFile" class="btn-choose">Choose New File</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5">{{ $note->description }}</textarea>
                </div>

                <div id="formErrors" style="display:none; color:#dc2626; font-size:13px;"></div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancelButton">Batalkan</button>
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        <section class="tips-container">
            <div class="tips-content">
                <div class="tips-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="tips-text">
                    <h3>Tips Mengedit Catatan</h3>
                    <ul>
                        <li>Pastikan judul catatan deskriptif dan mudah dicari</li>
                        <li>Gunakan format file yang didukung (PDF, DOC, DOCX)</li>
                        <li>Tambahkan deskripsi untuk memudahkan pencarian di masa depan</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <script>
        window.TUNOTES = {
            user: @json(['name' => Auth::user()->name, 'avatar' => Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png')]),
        };
    </script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/user-header.js') }}"></script>
    <script src="{{ asset('js/update-notes.js') }}"></script>
</body>
</html>
