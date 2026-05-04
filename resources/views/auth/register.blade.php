<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealMS - Create Account</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            background-color: #f0f2f5; 
        }

        .register-card {
            display: flex;
            width: 100%;
            max-width: 550px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .left-panel {
            width: 40%; /* Adjusted to match login page styling */
            background: #1a3b5c;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 20px;
        }

        .logo-box {
            width: 100%; /* Fill panel width to allow logo to scale up */
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-box img {
            width: 100%;
            height: auto;
            max-height: 150px; /* Matching login page logo size */
            object-fit: contain;
        }

        .right-panel {
            width: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 20px;
        }
        .form-box { width: 100%; }
        .form-box h2 { font-size: 18px; font-weight: 700; color: #1a3b5c; margin-bottom: 3px; }
        .form-box .subtitle { color: #888; font-size: 11px; margin-bottom: 12px; }

        .form-row { display: flex; gap: 8px; margin-bottom: 8px; }
        .form-group { display: flex; flex-direction: column; gap: 3px; flex: 1; }
        
        .field-label {
            font-size: 9px; font-weight: 700; color: #555;
            letter-spacing: 0.5px; text-transform: uppercase;
        }
        
        .form-input {
            width: 100%; padding: 7px 10px;
            border: 1px solid #ddd; border-radius: 5px;
            font-size: 12px; color: #333; outline: none;
        }
        .form-input:focus { border-color: #1a3b5c; }

        .btn-create {
            width: 100%; padding: 9px;
            background: #1a3b5c; color: white;
            border: none; border-radius: 5px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; margin-top: 6px;
        }
        .btn-create:hover { background: #c9952a; }

        .divider { text-align: center; color: #aaa; font-size: 11px; margin: 8px 0; }
        .signin-link { text-align: center; font-size: 12px; color: #888; }
        .signin-link a { color: #1a3b5c; font-weight: 600; text-decoration: none; }
        
        .error-text { color: #dc3545; font-size: 10px; margin-top: 1px; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="left-panel">
        <div class="logo-box">
            <img src="{{ asset('images/realms-logo-removebg-preview.png') }}" alt="RealMS Logo">
        </div>
    </div>

    <div class="right-panel">
        <div class="form-box">
            <h2>Create an Account</h2>
            <p class="subtitle">Fill in your details to get started</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">First Name</label>
                        <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}" required autofocus>
                        @error('first_name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="field-label">Last Name</label>
                        <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required>
                        @error('last_name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:8px;">
                    <label class="field-label">Email Address</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="margin-bottom:8px;">
                    <label class="field-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-input" value="{{ old('contact_number') }}" required>
                    @error('contact_number') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">Password</label>
                        <input type="password" name="password" class="form-input" required autocomplete="new-password">
                        @error('password') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="field-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn-create">Create Account</button>
            </form>

            <div class="divider">or</div>
            <div class="signin-link">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>