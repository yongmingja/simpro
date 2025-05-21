<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIMPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0b6db8, #5eb3f3, #a1c4fd);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fira Sans', sans-serif;
            margin: 0;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h4 {
            color: #333;
            font-weight: bold;
        }
        .form-label {
            text-align: left;
            display: block;
            font-weight: 500;
        }
        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 5px rgba(79, 172, 254, 0.5);
        }
        .btn-primary {
            background: #4facfe;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background: #00c6fe;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .position-relative {
            position: relative;
        }
        .logo {
            margin-bottom: 1rem;
            width: 100px;
        }
        .password-toggle {
            position: absolute;
            right: 1rem; /* Jarak ke kanan */
            top: 50%;
            transform: translateY(20%); /* Agar tepat di tengah vertikal */
            cursor: pointer;
            color: #6c757d;
            font-size: 1rem;
            z-index: 2; /* Pastikan berada di atas elemen lain */
        }

        .ikon {
            color: #000;
            font-size: inherit; /* Warna ikon agar senada */
            vertical-align: middle; /* Menyelaraskan ikon dengan teks */
        }


        @media (max-width: 768px) {
            .login-card {
                width: 90%;
                padding: 1.5rem;
            }
            .logo {
                width: 80px;
            }
            h4 {
                font-size: 1.5rem;
            }
            .btn-primary {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="warna">
        
    </div>
    <div class="login-card">
        <img src="{{asset('assets/img/logo-uvers.png')}}" class="logo" alt="logolpm">
        <h4>Welcome to SIMPR<i id="ganti-warna" class="bx bx-lemon ikon" style="cursor:pointer; font-size: 1.5rem;"></i></h4>
        <p class="text-muted">Sistem Informasi Proposal Kegiatan</p>
        <form id="loginForm" action="{{ route('postLogin') }}" method="POST">
            @csrf
            @if($errors->any())
            <div class="text-danger mb-2">{{ $errors->first() }}</div>
            @endif
            <div class="mb-3">
                <label for="user_id" class="form-label">ID Pengguna</label>
                <input type="text" id="user_id" name="user_id" class="form-control" placeholder="Masukkan ID pengguna" value="{{ old('user_id') }}" required>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required>
                <span class="password-toggle" onclick="togglePassword()">
                    <i class="bx bx-hide"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
        <p class="text-muted small-text mt-3">© Universitas Universal 2025</p>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }

        const warnaList = [
            'linear-gradient(135deg, #2c3e50, #4b79a1)', 
            'linear-gradient(135deg, #1a252f, #2c3e50)',
            'linear-gradient(135deg, #4facfe, #00f2fe)',
            'linear-gradient(135deg, #ff9a9e, #fad0c4, #fbc2eb)',
            'linear-gradient(135deg, #a1c4fd, #c2e9fb, #89f7fe)',
            'linear-gradient(135deg, #2b5876, #4e4376)',
            'linear-gradient(135deg, #0b6db8, #5eb3f3, #a1c4fd)',
            'linear-gradient(135deg, #ff7e5f, #feb47b, #ff9a44)',
            'linear-gradient(135deg, #0f2027, #203a43, #2c5364)',
            'linear-gradient(135deg, #1c1c3c, #493240, #6e45e2)'
        ];
        let currentWarnaIndex = 0;

        document.getElementById('ganti-warna').addEventListener('click', function() {
            const body = document.body;
            currentWarnaIndex = (currentWarnaIndex + 1) % warnaList.length; // Loop melalui daftar warna
            body.style.background = warnaList[currentWarnaIndex];
        });

    </script>
</body>
</html>
