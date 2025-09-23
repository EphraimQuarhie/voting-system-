<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Voting System</title>
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a4.5 4.5 0 002.343-7.971M18 18.72a4.5 4.5 0 00-4.041 3.197M18 18.72c1.652-.167 3.376-.046 5.043.252a5.524 5.524 0 01-1.63 2.502M18 18.72c-.183.018-.367.027-.551.033A5.524 5.524 0 0120.413 22.25c.343-.274.652-.58.94-.915M13.5 10.5h.008v.008h-.008v-.008zm1.5 5h.008v.008h-.008V15.5zm-3.257-2.733c-.23-.09-.47-.162-.716-.217l-.02-.005.02.005zM12 21a9 9 0 100-18 9 9 0 000 18z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-900">Create an Account</h1>
            <p class="text-gray-500 mt-2">Join to participate in the upcoming elections.</p>
        </div>

        <?php
        $success = '';
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
                $error = 'All fields are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } else {
                // Check for duplicate username
                $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error = 'Username already exists. Please choose another.';
                } else {
                    // Insert user into database
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                    $insert->bind_param('ss', $username, $hashedPassword);
                    if ($insert->execute()) {
                        $success = 'Registration successful! You can now <a href="login.php" class="text-indigo-600 underline">log in</a>.';
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                    $insert->close();
                }
                $stmt->close();
            }
        }
        ?>
        <form id="registrationForm" method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" aria-label="Username" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" aria-label="Email" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" aria-label="Password" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 pr-10" required>
                <button type="button" tabindex="-1" aria-label="Show password" onclick="togglePassword('password', 'eyeIcon1')" class="absolute right-3 top-9 text-gray-400 hover:text-gray-700 focus:outline-none">
                    <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9 7-9 9-9 9 4 9 9z" /></svg>
                </button>
            </div>
            <div class="mb-4 relative">
                <label for="confirmPassword" class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" aria-label="Confirm Password" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 pr-10" required>
                <button type="button" tabindex="-1" aria-label="Show confirm password" onclick="togglePassword('confirmPassword', 'eyeIcon2')" class="absolute right-3 top-9 text-gray-400 hover:text-gray-700 focus:outline-none">
                    <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9 7-9 9-9 9 4 9 9z" /></svg>
                </button>
                <p id="password-match-error" class="text-red-500 text-sm mt-1 hidden">Passwords do not match.</p>
            </div>
            <button type="submit" id="submitButton" class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-full hover:bg-indigo-700 transition duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:transform-none disabled:cursor-not-allowed">
                Sign Up
            </button>
        </form>

        <?php if ($error): ?>
            <div id="message" class="mt-6 text-center text-sm font-semibold rounded-lg p-3 bg-red-100 text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($success): ?>
            <div id="message" class="mt-6 text-center text-sm font-semibold rounded-lg p-3 bg-green-100 text-green-800">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <div id="message" class="mt-6 text-center text-sm font-semibold rounded-lg p-3 hidden"></div>

        <p class="text-center text-gray-500 mt-6">
            Already have an account? 
            <a href="login.php" class="text-indigo-600 font-bold hover:underline">Log in here</a>
        </p>
    </div>
    
    <script>
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirmPassword').value.trim();
            const submitButton = document.getElementById('submitButton');
            const passwordError = document.getElementById('password-match-error');

            const passwordsMatch = password === confirmPassword && password !== '';
            
            if (passwordsMatch) {
                passwordError.classList.add('hidden');
            } else {
                passwordError.classList.remove('hidden');
            }

            if (username && email && passwordsMatch) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        document.getElementById('registrationForm').addEventListener('input', validateForm);

        function togglePassword(fieldId, eyeId) {
            const pwd = document.getElementById(fieldId);
            const eye = document.getElementById(eyeId);
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
