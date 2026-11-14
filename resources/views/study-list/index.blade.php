<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>TuNotes | Study List</title>
    <link rel="stylesheet" href="{{ asset('css/StudyList.css') }}">
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tambahan kecil di atas StudyList.css: filter semester + aksi kartu */
        .semester-filter {
            height: 40px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #1f2937;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
        }
        /* Tinggi kartu mengikuti isi supaya ikon tidak tertekan saat nama panjang */
        .course-card { position: relative; height: auto; min-height: 218px; }
        .course-icon { flex-shrink: 0; }
        .course-card .btn--outline { margin-top: auto; }
        .course-actions {
            position: absolute;
            top: 16px;
            right: 16px;
            display: flex;
            gap: 4px;
        }
        .course-actions button {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
        }
        .course-actions button:hover { background: #f3f4f6; color: #1f2937; }
        .course-actions button[data-action="delete-course"]:hover { color: #ef4444; }
        .course-meta { margin: 0 0 12px; font-size: 13px; color: #6b7280; }
        .study-message { color: #6b7280; padding: 16px 32px; }
        .study-message--error { color: #b91c1c; }
        a.btn--outline { display: block; box-sizing: border-box; }
    </style>
</head>
<body>

    <div class="app-container">

        <aside class="sidebar">
            <header class="sidebar__header">
                <a href="{{ route('dashboard') }}" class="sidebar__logo">
                    <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
                </a>
            </header>

            <nav class="sidebar__nav">
                <ul class="nav-list">
                    <a href="{{ route('dashboard') }}" class="nav-link"><i class="ai-home-alt1"></i> Home</a>
                    <a href="{{ route('notes.page') }}" class="nav-link"><i class="ai-file"></i> My Notes</a>
                    <a href="#" class="nav-link"><i class="ai-heart"></i> Favorit</a>
                    <a href="{{ route('notes.upload') }}" class="nav-link"><i class="ai-cloud-upload"></i>Upload Notes</a>
                    <a href="#" class="nav-link"><i class="fi fi-rr-robot"></i> Notes AI</a>
                    <a href="{{ route('readings.index') }}" class="nav-link"><i class="ai-folder"></i> Notes Read</a>
                    <a href="{{ route('study-list.page') }}" class="nav-link active"><i class="ai-schedule"></i> Study List</a>
                    <a href="#" class="nav-link"><i class="ai-person"></i> Profil</a>
                </ul>
            </nav>
            <div class="logout">
                <a href="{{ route('logout') }}" class="nav-logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ai-sign-out"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            </div>
        </aside>

        <main class="main-content">

            <header class="content-header">
                <div class="content-header__info">
                    <h1>Study List – Rencana Belajar Semesteran</h1>
                    <p>Kelola daftar mata kuliah dan hubungkan dengan catatan kuliahmu.</p>
                </div>

                <div class="content-header__actions">
                    <select class="semester-filter" id="semesterFilter" aria-label="Filter semester">
                        <option value="">Semua Semester</option>
                    </select>
                    <button class="btn btn--primary" id="btnAddSemester">
                        <span>+ Tambah Semester Baru</span>
                    </button>
                </div>
            </header>

            <div id="semestersContainer"></div>

            <section class="summary-section">
                <h2 class="summary-section-title">Ringkasan Study Progress</h2>
                <div class="summary-grid">

                    <div class="summary-card summary-card--blue">
                        <div class="summary-icon"><i class="fa-solid fa-book"></i></div>
                        <div class="summary-number" id="statTotalCourses">0</div>
                        <div class="summary-label">Total Mata Kuliah</div>
                    </div>

                    <div class="summary-card summary-card--green">
                        <div class="summary-icon"><i class="fa-solid fa-sticky-note"></i></div>
                        <div class="summary-number" id="statTotalNotes">0</div>
                        <div class="summary-label">Total Catatan</div>
                    </div>

                    <div class="summary-card summary-card--purple">
                        <div class="summary-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div class="summary-number" id="statActiveSemesters">0</div>
                        <div class="summary-label">Semester Aktif</div>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <script>
        window.STUDY_LIST = { notesUrl: @json(route('notes.page')) };
    </script>
    <script src="{{ asset('js/study-modal.js') }}"></script>
    <script src="{{ asset('js/study-list.js') }}"></script>
</body>
</html>
