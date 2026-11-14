<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>TuNotes - My Notes</title>
    <link rel="stylesheet" href="{{ asset('css/Mynotes.css') }}">
    <!-- Lucide Icons (Menggunakan CDN untuk ikon) -->
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="app-container">

    <aside class="sidebar">
        <div class="sidebar__header">
           <a href="{{ route('dashboard') }}" class="top-header__logo">
             <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
            </a>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link"><i data-lucide="folder-dot" class="ai-home-alt1"></i> Home</a>
                </li>
                <li class="nav-item active">
                    <a href="{{ route('notes.page') }}" class="nav-link"><i data-lucide="folder-dot" class="ai-file"></i> My Notes</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i data-lucide="folder-dot" class="ai-heart"></i> Favorit</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notes.upload') }}" class="nav-link"><i data-lucide="folder-dot" class="ai-cloud-upload"></i> Upload Notes</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i data-lucide="folder-dot" class="fi fi-rr-robot"></i> Notes AI</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('readings.index') }}" class="nav-link"><i data-lucide="folder-dot" class="ai-folder"></i> Notes Read</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('study-list.page') }}" class="nav-link"><i data-lucide="folder-dot" class="ai-schedule"></i> Study List</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i data-lucide="folder-dot" class="ai-person"></i>Profil</a>
                </li>
            </ul>
        </nav>

        <div class="sidebar__footer">
            <a href="{{ route('logout') }}" class="btn-logout"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ai-sign-out"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </aside>

    <div class="main-content">

        <header class="content-header">
            <div class="header__left">
                <h1 class="page-title">My Notes</h1>
                <p class="page-subtitle">Track and manage your document submissions</p>
            </div>
            <div class="header__right">
                <button class="btn--icon">
                    <i class="ai-bell"></i>
                    <span class="badge" data-notif-badge>2</span>
                </button>
                <button class="btn--icon">
                  <i class="ai-envelope"></i>
                    <span class="badge" data-message-badge>3</span>
                </button>
                <button class="user-profile">
                    <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png') }}" alt="" class="avatar" data-user-avatar>
                    <span data-user-name>{{ Auth::user()->name }}</span>
                    <i class="ai-chevron-down"></i>
                </button>
            </div>
        </header>

        <div class="action-bar">
            <div class="filter-controls">
                <div class="dropdown">
                    <select class="select-box" id="categoryFilter" onchange="if (this.value) window.location.href = this.value;">
                        <option value="{{ route('notes.page') }}">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ route('notes.category', $category) }}"
                                {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <i class="ai-chevron-down-small  icon-dropdown"></i>
                </div>
                <div class="search-box">
                  <i class="ai-search icon-search"></i>
                    <input type="text" id="searchInput" placeholder="Search documents..." class="input-search">
                </div>
            </div>
            <a href="{{ route('notes.upload') }}"><button class="btn btn--primary"><i class="ai-plus"></i> Upload New</button></a>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; padding:16px 32px 0;">
            @foreach ($categories as $category)
                <a href="{{ route('notes.category', $category) }}"
                   style="text-decoration:none; background:#ffffff; border:1px solid #cbd5e1; color:#334155; border-radius:9999px; padding:6px 14px; font-size:13px;">
                    {{ $category }}
                </a>
            @endforeach

            <form action="{{ route('notes.categories.store') }}" method="POST"
                  style="display:flex; gap:8px; align-items:center; margin-left:auto;">
                @csrf
                <input type="text" name="name" maxlength="50" required placeholder="Tambah category baru..."
                       style="height:36px; border:1px solid #cbd5e1; border-radius:8px; padding:0 10px; font-size:13px;">
                <button type="submit" class="btn btn--primary" style="height:36px; padding:0 12px;">
                    <i class="ai-plus"></i> Add
                </button>
            </form>
        </div>

        @if (session('status'))
            <p style="padding:12px 32px 0; color:#16a34a; font-size:14px;">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p style="padding:12px 32px 0; color:#dc2626; font-size:14px;">{{ $errors->first() }}</p>
        @endif

        <div class="notes-scroll-wrapper">
            <div class="notes-grid" id="notesGrid">
                @forelse ($notes as $note)
                    <article class="note-card" data-search="{{ strtolower($note->display_title . ' ' . $note->course_name . ' ' . $note->lecturer_name) }}">
                        <img src="{{ asset('images/Mynotes' . (($loop->index % 8) + 1) . '.jpg') }}" alt="{{ $note->display_title }}" class="note-card__image">
                        <a href="{{ route('notes.category', $note->category ?: 'TuNotes') }}" class="note-card__body">
                            <span class="note-card__title">{{ $note->display_title }}</span>
                            <span class="note-card__author">{{ $note->course_name }}</span>
                            <span class="note-card__date">{{ $note->created_at?->format('d M Y') }}</span>
                        </a>
                    </article>
                @empty
                    <p style="grid-column: 1 / -1; color:#6b7280;">Belum ada catatan. Silakan upload catatan baru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<script>
    window.TUNOTES = {
        user: @json(['name' => Auth::user()->name, 'avatar' => Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png')]),
    };
</script>
<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/user-header.js') }}"></script>
<script src="{{ asset('js/my-notes.js') }}"></script>
</body>
</html>
