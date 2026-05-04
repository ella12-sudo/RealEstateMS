<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealMS - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            background-color: #f4f7f6; 
        }

        .login-card {
            display: flex;
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .left-panel {
            width: 40%; /* Slightly widened to give the logo more room */
            background: #1a3b5c;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 20px;
        }

        .logo-box {
            width: 100%; /* Changed to 100% to fill the panel width */
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-box img {
            width: 100%;
            height: auto;
            max-height: 150px; /* Limits height so it doesn't stretch too far */
            object-fit: contain;
        }

        .right-panel {
            width: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 25px 20px;
        }

        .form-box { width: 100%; }
        .form-box h2 { font-size: 18px; font-weight: 700; color: #1a3b5c; margin-bottom: 3px; }
        .form-box .subtitle { color: #888; font-size: 12px; margin-bottom: 15px; }

        .field-label { font-size: 10px; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 3px; }
        
        .form-input { 
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 10px;
            outline: none;
        }

        .btn-signin {
            width: 100%;
            padding: 9px;
            background: #1a3b5c;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
            font-size: 13px;
        }
        .btn-signin:hover { background: #c9952a; }

        .divider { text-align: center; color: #aaa; font-size: 11px; margin: 10px 0; }
        .signup-link { text-align: center; font-size: 12px; color: #888; }
        .signup-link a { color: #1a3b5c; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="left-panel">
            <div class="logo-box">
                <img src="{{ asset('images/realms-logo-removebg-preview.png') }}" alt="RealMS Logo">
            </div>
        </div>

        <div class="right-panel">
            <div class="form-box">
                <h2>WELCOME BACK</h2>
                <p class="subtitle">Sign in to your account</p>

                @if ($errors->has('email'))
                    <div style="color: #c00; font-size: 12px; margin-bottom: 10px;">
                        {{ $errors->first('email') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="role" id="role-input" value="admin">

                    <p class="field-label">EMAIL ADDRESS</p>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>

                    <p class="field-label">PASSWORD</p>
                    <input type="password" name="password" class="form-input" required>

                    <button type="submit" class="btn-signin">Sign In</button>
                </form>

                <div class="divider">or</div>
                <div class="signup-link">
                    New here? <a href="{{ route('register') }}">Sign up free</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>