<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Prismabit</title>
    <script src="../assets/js/cdn.tailwindcss.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: "Inter", sans-serif; }
        body { background: #0b0b0b; }
        .login-card {
            background: #1a1a1a;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .login-card:focus-within {
            border-color: #007BFF;
            box-shadow: 0 0 20px rgba(0,123,255,0.15);
        }
        .input-field {
            background: #0b0b0b;
            border: 1px solid rgba(255,255,255,0.15);
            transition: border-color 0.3s;
        }
        .input-field:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.2);
        }
        .btn-login {
            background: #007BFF;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.4);
        }
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .error-msg {
            background: rgba(220,53,69,0.1);
            border: 1px solid rgba(220,53,69,0.3);
            color: #ff6b6b;
        }
        .spinner {
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="../assets/images/prisma-logo.png" alt="Prismabit" class="h-12 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-white">Admin Panel</h1>
            <p class="text-gray-400 mt-2">Sign in to your account</p>
        </div>

        <div class="login-card rounded-2xl p-8">
            <div id="error-box" class="error-msg rounded-lg p-3 mb-6 text-sm hidden"></div>

            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                        class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500"
                        placeholder="Enter your username">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500"
                        placeholder="Enter your password">
                </div>

                <button type="submit" id="loginBtn"
                    class="btn-login w-full py-3 rounded-lg text-white font-semibold flex items-center justify-center gap-2">
                    <span id="btnText">Sign In</span>
                    <div id="btnSpinner" class="spinner hidden"></div>
                </button>
            </form>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            <a href="../index.html" class="text-[#007BFF] hover:underline">&larr; Back to Website</a>
        </p>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const errorBox = document.getElementById('error-box');

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            btn.disabled = true;
            btnText.textContent = 'Signing in...';
            btnSpinner.classList.remove('hidden');
            errorBox.classList.add('hidden');

            try {
                const res = await fetch('../api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await res.json();

                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorBox.textContent = data.message;
                    errorBox.classList.remove('hidden');
                }
            } catch (err) {
                errorBox.textContent = 'Something went wrong. Please try again.';
                errorBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Sign In';
                btnSpinner.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
