
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan - TuNotes</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #F7F9FC;
            color: #263449;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 245px;
            background: white;
            border-right: 1px solid #E8EDF4;
            padding: 28px 18px;
            flex-shrink: 0;
        }

        .logo {
            width: 195px;
            max-width: 100%;
            display: block;
            margin: 0 auto 38px;
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
        }

        .menu a:hover,
        .menu a.active {
            background: #EFF6FF;
            color: #3B82F6;
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
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 26px;
            margin-bottom: 7px;
        }

        .topbar p {
            color: #8290A5;
            font-size: 14px;
        }

        .profile {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            object-fit: cover;
        }

        .form-card {
            background: white;
            border: 1px solid #E8EDF4;
            border-radius: 14px;
            padding: 28px;
            max-width: 750px;
        }

        .form-card h2 {
            font-size: 19px;
            margin-bottom: 8px;
        }

        .form-description {
            color: #8290A5;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 9px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E1E7EF;
            border-radius: 8px;
            outline: none;
            font-size: 13px;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px #3B82F61A;
        }

        .error {
            color: #DC2626;
            font-size: 12px;
            margin-top: 6px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #3B82F6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563EB;
        }

        .btn-secondary {
            background: #EFF2F7;
            color: #64748B;
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

            .form-card {
                padding: 20px;
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
            <a href="#">Study List</a>
            <a href="#">Profil</a>
            <a href="#">Logout</a>
        </nav>
    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Edit Catatan</h1>
                <p>Perbarui informasi catatan belajarmu.</p>
            </div>

            <img src="{{ asset('assets/images/user1.png') }}"
                 alt="Profil Pengguna" class="profile">
        </header>

        <section class="form-card">
            <h2>Informasi Catatan</h2>
            <p class="form-description">
                Ubah informasi catatan sesuai kebutuhanmu.
            </p>

            <form action="{{ route('readings.update', $reading->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="note_title">Judul Catatan</label>
                    <input type="text"
                           id="note_title"
                           name="note_title"
                           value="{{ old('note_title', $reading->note_title) }}"
                           placeholder="Masukkan judul catatan">

                    @error('note_title')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status Belajar</label>
                    <select name="status" id="status">
                        <option value="Not Started"
                            {{ old('status', $reading->status) == 'Not Started' ? 'selected' : '' }}>
                            Belum Dibaca
                        </option>

                        <option value="In Progress"
                            {{ old('status', $reading->status) == 'In Progress' ? 'selected' : '' }}>
                            Sedang Dipelajari
                        </option>

                        <option value="Completed"
                            {{ old('status', $reading->status) == 'Completed' ? 'selected' : '' }}>
                            Selesai
                        </option>
                    </select>

                    @error('status')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="progress">Progress (%)</label>
                    <input type="number"
                           id="progress"
                           name="progress"
                           min="0"
                           max="100"
                           value="{{ old('progress', $reading->progress) }}"
                           placeholder="0 - 100">

                    @error('progress')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_date">Target Tanggal</label>
                    <input type="date"
                           id="target_date"
                           name="target_date"
                           value="{{ old('target_date', $reading->target_date) }}">

                    @error('target_date')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('readings.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </section>

    </main>
</div>
</body>
</html>
