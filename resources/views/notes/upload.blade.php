<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <meta charset="UTF-8">
    <title>Upload Notes | TuNote</title>
    <!-- Hubungkan ke file CSS murni -->
    <link rel="stylesheet" href="{{ asset('css/UploadNotes.css') }}">
    <!-- Lucide Icons (Menggunakan CDN untuk ikon) -->
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>
<body class="upload-notes-page">
    <div class="app-container">
        <!-- 1. Sidebar / Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="top-header__logo">
                 <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item">
                    <i data-lucide="folder-dot" class="ai-home-alt1"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('notes.page') }}" class="nav-item">
                     <i data-lucide="folder-dot" class="ai-file"></i>
                    <span>My Notes</span>
                </a>
                <a href="#" class="nav-item">
                    <i data-lucide="folder-dot" class="ai-heart"></i>
                    <span>Favorit</span>
                </a>
                <a href="{{ route('notes.upload') }}" class="nav-item nav-item-active">
                    <i data-lucide="folder-dot" class="ai-cloud-upload"></i>
                    <span>Upload Notes</span>
                </a>
                <a href="#" class="nav-item nav-item">
                    <i data-lucide="folder-dot" class="fi fi-rr-robot"></i>
                    <span>Notes AI</span>
                </a>
                <a href="#" class="nav-item nav-item">
                    <i data-lucide="folder-dot" class="ai-folder"></i>
                    <span>Notes Read</span>
                </a>
                <a href="#" class="nav-item nav-item">
                    <i data-lucide="folder-dot" class="ai-schedule"></i>
                    <span>Study List</span>
                </a>
                <a href="#" class="nav-item">
                    <i data-lucide="folder-dot" class="ai-person"></i>
                    <span>Profil</span>
                </a>
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

        <!-- 2. Main Content Area (Header + Body) -->
        <main class="main-content-area">

            <!-- Header Top Bar -->
            <header class="page-header">

                <div class="header-title-block">
                    <h1 class="title">Upload Notes</h1>
                    <p class="subtitle">Your saved notes and study materials</p>
                </div>

                <!-- Aksi Pengguna (Notifikasi & Profil) -->
                <div class="user-actions">
                  <button class="action-btn" aria-label="Notifikasi">
                    <i class="ai-bell"></i>
                    <span class="badge" data-notif-badge>3</span>
                  </button>
                  <button class="action-btn" aria-label="Pesan">
                    <i class="ai-envelope"></i>
                    <span class="badge" data-message-badge>2</span>
                  </button>
                  <div class="user-menu">
                   <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png') }}" alt="" class="user-avatar" data-user-avatar>
                   <span data-user-name>{{ Auth::user()->name }}</span>
                  </div>
                </div>
            </header>

            <!-- Main Upload Form Section -->
            <section class="main-body">

                <div class="upload-form-wrapper">

                    <div class="section-header">
                        <div class="icon-bg-wrapper"><i data-lucide="file-up" class="ai-cloud-upload"></i></div>
                        <h2 class="section-title">Upload Your Notes</h2>
                        <p class="section-subtitle">Share your study materials with fellow students</p>
                    </div>

                    <!-- Formulir Utama -->
                    <form class="notes-details-section" id="uploadForm" novalidate>

                        <!-- Input 1: Course Name -->
                        <div class="form-group">
                            <label for="courseName" class="form-label">Course Name</label>
                            <input type="text" id="courseName" name="course_name" placeholder="e.g., Computer Science 101" class="form-input-field" />
                        </div>

                        <!-- Input 2: Lecturer Name -->
                        <div class="form-group">
                            <label for="lecturerName" class="form-label">Lecturer Name</label>
                            <input type="text" id="lecturerName" name="lecturer_name" placeholder="e.g., Dr. Smith" class="form-input-field" />
                        </div>

                        <!-- Input 3: Category -->
                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-input-field">
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                                <option value="__new__">+ Tambah category baru…</option>
                            </select>
                            <input type="text" id="newCategory" placeholder="Tulis nama category baru"
                                   class="form-input-field" style="display:none; margin-top:8px;">
                        </div>

                        <!-- Input 4: Tags -->
                        <div class="form-group">
                            <label for="tags" class="form-label">Tags</label>
                            <div class="tag-input-wrapper">
                                <!-- Placeholder Tag -->
                                <input type="text" id="tags" name="tags" placeholder="Tambahkan Tag... (pisahkan dengan koma)" class="tag-input" />
                            </div>
                        </div>

                        <!-- Input 5: Description (Span 2 columns di desktop) -->
                        <div class="form-group">
                            <label for="description" class="form-label">Description / Notes Content</label>
                            <textarea id="description" name="description" rows="4" placeholder="Masukan teks....." class="form-textarea-field"></textarea>
                        </div>

                        <!-- Input File Tersembunyi -->
                        <input
                            type="file"
                            id="fileInput"
                            name="file"
                            class="hidden-input-file"
                            accept=".pdf,.doc,.docx,.txt,.ppt,.pptx"
                        />

                        <!-- File Dropzone (Pemicu Input File) -->
                        <div class="upload-dropzone" id="dropzoneArea">
                            <i data-lucide="file-text" class="ai-share-box"></i>
                            <p class="dropzone-text-main">Drop your files here</p>
                            <p class="dropzone-text-hint">
                                <span>or </span>
                                <span class="browse-link">browse</span>
                                <span> to choose files</span>
                            </p>
                            <!-- Status File Akan Diupdate Oleh JS -->
                            <div id="fileStatus" class="file-status">Belum ada file dipilih.</div>
                            <p class="dropzone-text-limit">Maximum file size: 10MB per file</p>
                            <p class="dropzone-text-support">Supports: PDF, DOC, DOCX, TXT, PPT, PPTX</p>
                        </div>

                        <div id="formErrors" style="display:none; grid-column: 1 / -1; color:#dc2626; font-size:13px;"></div>

                        <!-- Tombol Submit (Span 2 columns di desktop) -->
                        <button type="submit" class="upload-submit-button" id="submitButton">
                            <span>Upload Notes</span>
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </div>
    <script>
        window.TUNOTES = {
            user: @json(['name' => Auth::user()->name, 'avatar' => Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png')]),
            notesUrl: @json(route('notes.page')),
        };
    </script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/user-header.js') }}"></script>
    <script src="{{ asset('js/upload-notes.js') }}"></script>
</body>
</html>
