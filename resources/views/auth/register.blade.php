<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>Register - TuNotes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <link rel="stylesheet" href="{{ asset('css/Register.css') }}">
</head>

<body>
    <main class="register-container">
        <div class="auth-card">
            <header class="auth-header">
                <div class="logo-wrapper">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/Logo-Tunotes.png') }}" alt="TuNotes Icon">
                    </a>
                </div>
                <p>Create your student account</p>
            </header>

            <form class="auth-form" id="registerForm" action="{{ route('register') }}" method="POST" novalidate>
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name Lengkap</label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Enter your First name" maxlength="100" required>
                        </div>
                        @error('name')
                            <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="student@university.edu" required>
                    </div>
                    @error('email')
                        <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="Choose a username" required minlength="3" maxlength="50"
                            pattern="[A-Za-z0-9_]{3,50}">
                    </div>
                    @error('username')
                        <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Create a strong password"
                            required autocomplete="new-password">
                    </div>
                    @error('password')
                        <span style="color: #dc2626; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi password Anda" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#">terms & conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn-primary">
                    Register Now
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
                <div class="divider">
                    <span>Or continue with</span>
                </div>
                <button class="btn-google">
                    <i class="ai-google-fill"></i>
                </button>
            </div>
            <footer class="bottom-caption">
                By registering, you're joining thousands of students sharing knowledge
            </footer>
        </div>
    </main>
</body>

</html>
