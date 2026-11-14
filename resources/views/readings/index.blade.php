```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuNotes - Board Kuliah</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --primary-light: #EFF6FF;
            --text: #263449;
            --muted: #8290A5;
            --border: #E8EDF4;
            --background: #F7F9FC;
        }

        body {
            background: var(--background);
            color: var(--text);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 245px;
            background: white;
            border-right: 1px solid var(--border);
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .logo {
            width: 195px;
            max-width: 100%;
            margin: 0 auto 38px;
            display: block;
            object-fit: contain;
        }

        .menu-label {
            font-size: 11px;
            color: #A0AABC;
            font-weight: bold;
            margin: 10px 12px 12px;
            letter-spacing: 1px;
        }

        .menu a {
            display: block;
            padding: 12px 14px;
            margin-bottom: 5px;
            color: #718096;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
            transition: .2s;
        }

        .menu a:hover,
        .menu a.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: bold;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 26px;
            margin-bottom: 7px;
        }

        .topbar p {
            color: var(--muted);
            font-size: 14px;
        }

        .profile {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            object-fit: cover;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 13px;
            border: 1px solid var(--border);
        }

        .stat-card p {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            color: var(--primary);
            font-size: 27px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .search {
            flex: 1;
            max-width: 380px;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 9px;
            outline: none;
            font-size: 13px;
            background: white;
        }

        .search:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px #3B82F61A;
        }

        .add-button {
            background: var(--primary);
            color: white;
            padding: 12px 18px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: .2s;
        }

        .add-button:hover {
            background: var(--primary-dark);
        }

        .board {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            align-items: start;
        }

        .column {
            padding: 16px;
            border-radius: 13px;
            min-height: 260px;
        }

        .column.not-started {
            background: #FFF0F1;
        }

        .column.in-progress {
            background: #FFF5E8;
        }

        .column.completed {
            background: #EAF7EE;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .column-header h3 {
            font-size: 14px;
        }

        .count {
            background: white;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 12px;
            color: #718096;
        }

        .note-card {
            background: white;
            padding: 17px;
            border-radius: 11px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px #2634490A;
        }

        .note-card h4 {
            font-size: 14px;
            margin-bottom: 14px;
            overflow-wrap: anywhere;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 7px;
        }

        .progress-bar {
            height: 7px;
            background: #EDF0F5;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 13px;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
        }

        .target-date {
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 14px;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #F0F2F6;
            padding-top: 12px;
        }

        .edit-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .delete-button {
            background: none;
            border: none;
            color: #E05252;
            font-size: 12px;
            cursor: pointer;
        }

        .empty {
            color: #9AA5B5;
            text-align: center;
            font-size: 12px;
            padding: 35px 10px;
        }

        .tips {
            margin-top: 25px;
            padding: 22px;
            border-radius: 13px;
            background: linear-gradient(120deg, #3B82F6, #60A5FA);
            color: white;
        }

        .tips h3 {
            font-size: 15px;
            margin-bottom: 7px;
        }

        .tips p {
            font-size: 13px;
            line-height: 1.6;
            opacity: .95;
        }

        .alert {
            background: #DCFCE7;
            color: #166534;
            padding: 12px 15px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        @media (max-width: 1000px) {
            .board {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 75px;
                padding: 20px 8px;
            }

            .logo {
                width: 55px;
                margin-bottom: 25px;
            }

            .menu-label {
                display: none;
            }

            .menu a {
                font-size: 0;
                text-align: center;
                padding: 13px 5px;
            }

            .main {
                padding: 20px 14px;
            }

            .topbar h1 {
                font-size: 21px;
            }

            .stats {
                gap: 10px;
            }

            .stat-card {
                padding: 15px;
            }

            .toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .search {
                max-width: none;
                width: 100%;
            }

            .add-button {
                text-align: center;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <img src="{{ asset('assets/images/Logo-Tunotes.png') }}"
             alt="TuNotes" class="logo">

        <div class="menu-label">MENU UTAMA</div>

        <nav class="menu">
            <a href="#">Home</a>
            <a href="#">My Notes</a>
            <a href="#">Favorit</a>
            <a href="#">Upload Notes</a>
            <a href="#">Notes AI</a>
            <a href="{{ route('readings.index') }}" class="active">Notes Read</a>
            <a href="{{ route('study-list.page') }}">Study List</a>
            <a href="#">Profil</a>
            <a href="#">Logout</a>
        </nav>
    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Board Kuliah</h1>
                <p>Catatan kuliah lebih visual dan mudah diatur.</p>
            </div>

            <img src="{{ asset('assets/images/user1.png') }}"
                 alt="Profil Pengguna" class="profile">
        </header>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @php
            $total = $readings->count();
            $completed = $readings->whereIn('status', ['Completed', 'Selesai'])->count();
        @endphp

        <section class="stats">
            <div class="stat-card">
                <p>Total Catatan</p>
                <h2>{{ $total }}</h2>
            </div>

            <div class="stat-card">
                <p>Catatan Selesai</p>
                <h2>{{ $completed }}</h2>
            </div>
        </section>

        <div class="toolbar">
            <input type="text"
                   id="searchInput"
                   class="search"
                   placeholder="Cari judul catatan...">

            <a href="{{ route('readings.create') }}" class="add-button">
                + Tambah Catatan
            </a>
        </div>

        <section class="board">

            @php
                $columns = [
                    [
                        'title' => 'Belum Dibaca',
                        'class' => 'not-started',
                        'statuses' => ['Belum Dibaca', 'Not Started']
                    ],
                    [
                        'title' => 'Sedang Dipelajari',
                        'class' => 'in-progress',
                        'statuses' => ['Sedang Dipelajari', 'In Progress']
                    ],
                    [
                        'title' => 'Selesai',
                        'class' => 'completed',
                        'statuses' => ['Completed', 'Selesai']
                    ],
                ];
            @endphp

            @foreach($columns as $column)
                @php
                    $items = $readings->whereIn('status', $column['statuses']);
                @endphp

                <div class="column {{ $column['class'] }}">
                    <div class="column-header">
                        <h3>{{ $column['title'] }}</h3>
                        <span class="count">{{ $items->count() }}</span>
                    </div>

                    @forelse($items as $reading)
                        <article class="note-card">
                            <h4>{{ $reading->note_title }}</h4>

                            <div class="progress-info">
                                <span>Progress</span>
                                <span>{{ $reading->progress }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill"
                                     style="width: {{ $reading->progress }}%">
                                </div>
                            </div>

                            <p class="target-date">
                                Target:
                                {{ \Carbon\Carbon::parse($reading->target_date)->format('d M Y') }}
                            </p>

                            <div class="card-actions">
                                <a href="{{ route('readings.edit', $reading->id) }}"
                                   class="edit-link">Edit</a>

                                <form action="{{ route('readings.destroy', $reading->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="delete-button">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="empty">Belum ada catatan.</p>
                    @endforelse
                </div>
            @endforeach

        </section>

        <section class="tips">
            <h3>Tips Belajar Hari Ini</h3>
            <p>
                Atur waktu belajar dan selesaikan catatan secara bertahap.
                Jangan lupa perbarui progress belajarmu!
            </p>
        </section>

    </main>
</div>

<script>
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase();

        document.querySelectorAll('.note-card').forEach(function (card) {
            const title = card.querySelector('h4').textContent.toLowerCase();

            card.style.display = title.includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>
```