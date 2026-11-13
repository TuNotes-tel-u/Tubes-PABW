<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>Dashboard - TuNotes</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>

<body>

    <header class="top-header">
        <a href="{{ route('dashboard') }}" class="top-header__logo">
            <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
        </a>

        <form class="search-bar" action="{{ route('dashboard') }}" method="GET">
            <i class="ai-search"></i>
            <input type="search" name="query" value="{{ request('query') }}"
                placeholder="Cari catatan, mata kuliah, atau dosen...">
        </form>

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
                <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png') }}"
                    alt="Avatar" class="user-avatar" data-user-avatar>
                <span id="userMenuName" data-user-name>{{ Auth::user()->name }}</span>
                <i class="ai-chevron-down"></i>
            </div>
        </div>
    </header>

    <aside class="sidebar-nav">
        <nav class="main-nav">
            <ul>
                <li>
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <i class="ai-home-alt1"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('notes.page') }}" class="nav-link">
                        <i class="ai-file"></i>
                        <span>My Notes</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="ai-heart"></i>
                        <span>Favorit</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('notes.upload') }}" class="nav-link">
                        <i class="ai-cloud-upload"></i>
                        <span>Upload Notes</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="fi fi-rr-robot"></i>
                        <span>Notes AI</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="ai-folder"></i>
                        <span>Notes Read</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="ai-schedule"></i>
                        <span>Study List</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="ai-person"></i>
                        <span>Profil</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="logout">
            <a href="{{ route('logout') }}" id="logoutLink" class="nav-logout"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ai-sign-out"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="main-content-header">
            <section class="welcome-banner">
                <h1 id="greeting">Halo, {{ Auth::user()->name }} !</h1>
                <p>Siap belajar lebih cerdas hari ini?</p>
            </section>
            <nav class="content-tabs">
                <button class="tab-btn {{ request('sort', 'newest') === 'newest' ? 'active' : '' }}" data-sort="newest">Terbaru</button>
                <button class="tab-btn {{ request('sort') === 'popular' ? 'active' : '' }}" data-sort="popular">Populer</button>
                <button class="tab-btn {{ request('sort') === 'rating' ? 'active' : '' }}" data-sort="rating">Rekomendasi AI</button>
            </nav>
        </div>
        <div class="notes-grid" id="notesGrid">
            @forelse ($notes as $note)
                <article class="note-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="ai-file"></i></div>
                        <button class="like-btn" type="button" aria-label="Favorit"><i class="ai-heart"></i></button>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('notes.category', $note->category ?: 'TuNotes') }}">
                            <h3>{{ $note->display_title }}</h3>
                        </a>
                        <p>{{ $note->course_name }}</p>
                        <p>{{ $note->lecturer_name ?: 'Dosen tidak diketahui' }}</p>
                    </div>
                    <div class="card-footer">
                        <span class="badge-{{ $note->file_type }}">{{ strtoupper($note->file_type) }}</span>
                        <span class="meta-item"><i class="ai-star-fill"></i>{{ $note->avg_rating }}</span>
                        <span class="meta-date">{{ $note->created_at?->format('d M Y') }}</span>
                    </div>
                </article>
            @empty
                <p class="empty-state" style="grid-column: 1 / -1; color:#6b7280;">
                    Belum ada catatan publik. Yuk upload catatan pertamamu!
                </p>
            @endforelse
        </div>
    </main>

    <aside class="sidebar-right">
        <h3>Progress Belajar</h3>
        <div class="progress-card">
            <div class="progress-header">
                <span>Catatan Dibaca</span>
                <span id="progressNumber">0/20</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
            </div>
            <p id="progressMessage">Memuat progress…</p>
        </div>
    </aside>

    <script>
        window.TUNOTES = {
            user: @json(['name' => Auth::user()->name, 'avatar' => Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/user1.png')]),
            notesCount: {{ $notes->count() }},
        };
    </script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/user-header.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>
