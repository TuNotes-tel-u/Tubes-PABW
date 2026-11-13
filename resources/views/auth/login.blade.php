<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>Login - TuNotes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>

<body class="login-page-background">
    <main class="login-card">
        <header class="login-card__header">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
            </a>
            <p>Catatan kuliah cerdas untuk mahasiswa Indonesia.</p>
        </header>

        <form class="login-form" action="{{ route('login') }}" method="POST">
            @csrf

            <div class="login-form__header">
                <h2>Masuk</h2>
                <p>Selamat datang kembali di TuNotes</p>
            </div>

            @if (session('error'))
                <p style="color: #dc2626; font-size: 14px; text-align: center;">{{ session('error') }}</p>
            @endif

            @if (session('status'))
                <p style="color: #16a34a; font-size: 14px; text-align: center;">{{ session('status') }}</p>
            @endif

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-with-icon">
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="student@university.edu" required autofocus autocomplete="username">
                    <i class="ai-envelope input-icon"></i>
                </div>
                @error('email')
                    <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required
                        autocomplete="current-password">
                    <i class="ai-lock-on input-icon"></i>
                </div>
                @error('password')
                    <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>

            <a href="#" class="form-forgot-password">Lupa password?</a>

            <button type="submit" class="btn btn--primary btn--full-width">Masuk</button>

            <p class="form-register-link">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </form>
    </main>
</body>

</html>
