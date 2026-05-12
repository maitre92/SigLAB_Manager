<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - sigLAB Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --login-primary: #152645;
            --login-accent: #4060a0;
            --login-danger: #dc2626;
            --login-bg: #e8efff;
            --login-text: #1f2937;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(135deg, rgba(21, 38, 69, 0.92), rgba(64, 96, 160, 0.78)),
                radial-gradient(circle at top left, rgba(255,255,255,0.28), transparent 34%),
                var(--login-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--login-text);
            padding: 24px;
        }

        .login-container {
            width: 100%;
            max-width: 520px;
        }

        .login-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.26);
            overflow: hidden;
        }

        .login-header {
            background: var(--login-primary);
            color: #fff;
            padding: 30px 34px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            margin-bottom: 14px;
            font-size: 22px;
        }

        .login-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 700;
            letter-spacing: 0;
        }

        .login-header p {
            margin: 8px 0 0;
            opacity: 0.78;
        }

        .login-body {
            background: #fff;
            padding: 34px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #d7dee9;
            padding: 12px 14px;
            min-height: 46px;
        }

        .form-control:focus {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 0.2rem rgba(64, 96, 160, 0.16);
        }

        .invalid-feedback {
            display: block;
        }

        .btn-login {
            background: var(--login-primary);
            border-color: var(--login-primary);
            border-radius: 8px;
            min-height: 46px;
            font-weight: 700;
            width: 100%;
            color: #fff;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: #0f1c34;
            border-color: #0f1c34;
            color: #fff;
        }

        .login-link {
            color: var(--login-accent);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <main class="login-container">
        <div class="card login-card">
            <div class="login-header">
                <div class="brand-mark">
                    <i class="fas fa-flask"></i>
                </div>
                <h1><span style="color:#6f8bd8;">sig</span><span style="color:#ef4444;">LAB</span> Manager</h1>
                <p>Connectez-vous à votre espace de gestion.</p>
            </div>

            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <strong>Connexion impossible</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nom@exemple.com"
                               autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Votre mot de passe">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="remember"
                                   id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a class="login-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-right-to-bracket"></i> Se connecter
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
