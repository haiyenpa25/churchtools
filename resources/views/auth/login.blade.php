<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập — ChurchTools</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --navy:  #07090f;
            --card-bg: rgba(13, 20, 38, 0.45);
            --gold:  #c9973a;
            --gold2: #e8b85a;
            --teal:  #1a6b5a;
            --teal2: #22876f;
            --text:  #e8e6f0;
            --muted: #8b92aa;
            --glass-border: rgba(255, 255, 255, 0.08);
            --focus-border: rgba(34, 135, 111, 0.6);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--navy);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 1.5rem;
        }

        /* Dynamic background blobs */
        body::before {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(26,107,90,0.2) 0%, transparent 70%);
            top: -10%;
            left: -10%;
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
            animation: float-slow 15s infinite alternate;
        }

        body::after {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(201,151,58,0.12) 0%, transparent 70%);
            bottom: -10%;
            right: -10%;
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
            animation: float-slow 15s infinite alternate-reverse;
        }

        @keyframes float-slow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 40px) scale(1.15); }
        }

        /* Glassmorphism Container */
        .login-container {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.75rem 2.25rem;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.8), 0 0 1px var(--glass-border) inset;
            position: relative;
            z-index: 10;
            animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slide-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo styling */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--text);
            margin-bottom: 2rem;
            text-align: center;
        }

        .brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--teal), var(--teal2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(26,107,90,0.4);
        }

        .brand span {
            color: var(--gold2);
        }

        .title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.5rem;
            font-weight: 750;
            text-align: center;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .subtitle {
            font-size: 0.825rem;
            color: var(--muted);
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Input fields */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 0.4rem;
            padding-left: 2px;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
            color: #fff;
            outline: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: inherit;
        }

        .form-input:focus {
            border-color: var(--teal2);
            background: rgba(13, 20, 38, 0.65);
            box-shadow: 0 0 0 4px rgba(34, 135, 111, 0.18);
        }

        /* Remember Checkbox */
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input {
            cursor: pointer;
            accent-color: var(--teal2);
            width: 15px;
            height: 15px;
        }

        .remember-label {
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* Action button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--teal), #1f8870);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 15px rgba(26,107,90,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26,107,90,0.5);
            background: linear-gradient(135deg, #1b7361, #25a185);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Errors and Alerts */
        .alert-error {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.25);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .alert-icon {
            font-size: 1.1rem;
            color: #f87171;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-text {
            font-size: 0.8rem;
            color: #fca5a5;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Back to portal link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.8rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--gold2);
        }
    </style>
</head>
<body>

    <div class="login-container">
        {{-- Brand Logo --}}
        <div class="brand">
            <div class="brand-icon">✝</div>
            Church<span>Tools</span>
        </div>

        <h2 class="title">Xin chào</h2>
        <p class="subtitle">Đăng nhập tài khoản để vào hệ thống slide</p>

        {{-- Error Alerts --}}
        @if ($errors->any())
            <div class="alert-error">
                <span class="alert-icon">⚠️</span>
                <span class="alert-text">
                    {{ $errors->first('username') ?: $errors->first() }}
                </span>
            </div>
        @endif

        {{-- Login Form --}}
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="username">Tên đăng nhập</label>
                <input class="form-input" 
                       type="text" 
                       id="username" 
                       name="username" 
                       placeholder="Nhập haiyenpa25..." 
                       value="{{ old('username') }}" 
                       required 
                       autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label class="form-label" for="password">Mật khẩu</label>
                <input class="form-input" 
                       type="password" 
                       id="password" 
                       name="password" 
                       placeholder="••••••••••••" 
                       required>
            </div>

            <label class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <span class="remember-label">Duy trì đăng nhập</span>
            </label>

            <button type="submit" class="btn-submit">
                <span>Vào Hệ Thống</span> 🔒
            </button>
        </form>

        <a href="{{ url('/') }}" class="back-link">← Quay về Trang chủ</a>
    </div>

</body>
</html>
