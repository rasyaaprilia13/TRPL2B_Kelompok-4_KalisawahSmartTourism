<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Account - Kali Sawah</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            height: 100vh;
            background-color: #ffffff;
        }

        .left {
            width: 50%;
            background: url('/uploud/foto.jpeg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .overlay-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(2px);
            width: 100%;
            height: 100%;
            border-radius: 30px;
        }

        .right {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .form-box h2 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 40px;
            text-align: center;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        button {
            width: 100%;
            padding: 16px;
            background: #0047ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background: #0036c7;
        }

        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="left">
    <div class="overlay-card"></div>
</div>

<div class="right">
    <div class="form-box">
        <h2>Login Account</h2>

        <!-- 🔥 FORM FIX -->
        <form method="POST" action="/admin/login">
            @csrf

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <!-- ERROR -->
        @if(session('error'))
            <div class="error">
                {{ session('error') }}
            </div>
        @endif

    </div>
</div>

</body>
</html>
