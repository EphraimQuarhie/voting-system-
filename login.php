<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white p-10 rounded-3xl shadow-lg">
        <div class="text-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-indigo-600 mx-auto mb-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
            <p class="text-gray-500 mt-2">Log in to cast your vote.</p>
        </div>

        <?php
        session_start();
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if ($username === '' || $password === '') {
                $error = 'Please enter both username and password.';
            } else {
                $stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $hashedPassword);
                    $stmt->fetch();
                    if (password_verify($password, $hashedPassword)) {
                        $_SESSION['user_id'] = $user_id;
                        if (strtolower($username) === 'admin') {
                            header('Location: admin.php');
                        } else {
                            header('Location: vote.php');
                        }
                        exit();
                    } else {
                        $error = 'Invalid username or password.';
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
                $stmt->close();
            }
        }
        ?>
        <form id="loginForm" method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" aria-label="Username" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required autofocus>
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" aria-label="Password" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 pr-10" required>
                <button type="button" tabindex="-1" aria-label="Show password" onclick="togglePassword()" class="absolute right-3 top-9 text-gray-400 hover:text-gray-700 focus:outline-none">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9 7-9 9-9 9 4 9 9z" /></svg>
                </button>
            </div>
            <button type="submit" id="submitButton" class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-full hover:bg-indigo-700 transition duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:transform-none disabled:cursor-not-allowed">
                Log In
            </button>
        </form>

        <?php if ($error): ?>
            <div id="message" class="mt-6 text-center text-sm font-semibold rounded-lg p-3 bg-red-100 text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <p class="text-center text-gray-500 mt-6">
            Don't have an account yet? 
            <a href="register.php" class="text-indigo-600 font-bold hover:underline">Sign up here</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19.5c-2.025 0-3.938-.586-5.537-1.59a9.978 9.978 0 01-3.356-3.36M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9c0-1.657.672-3.157 1.793-4.293" />';
            } else {
                pwd.type = 'password';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9 7-9 9-9 9 4 9 9z" />';
            }
        }
    </script>
</body>
</html>
