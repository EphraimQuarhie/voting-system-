<?php
include 'db.php';
session_start();
// Only allow admin
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($username);
    if ($stmt->fetch() && $username === 'admin') {
        $isAdmin = true;
    }
    $stmt->close();
}
if (!$isAdmin) {
    header('Location: login.php');
    exit();
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = trim($_POST['name']);
    $stmt = $conn->prepare('INSERT INTO candidates (name) VALUES (?)');
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Candidate added successfully!</div>';
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error: ' . htmlspecialchars($stmt->error) . '</div>';
    }
    $stmt->close();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-200 mt-10">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-700">Add New Candidate</h1>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="">
            <div class="flex flex-col gap-4">
                <label for="name" class="block text-gray-700 font-medium">Candidate Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200" required>
                <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-indigo-700 transition duration-300 shadow-md transform hover:scale-105">Add Candidate</button>
            </div>
        </form>
        <a href="admin.php" class="block mt-8 text-center text-indigo-600 hover:underline">Back to Admin</a>
    </div>
</body>
</html>
