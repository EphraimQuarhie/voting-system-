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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    if ($title && $start_date && $end_date) {
        $stmt = $conn->prepare('INSERT INTO elections (title, description, start_date, end_date) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $title, $description, $start_date, $end_date);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Election created successfully!</div>';
        } else {
            $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Please fill in all required fields.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create Election</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-gray-800 shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Admin Panel</h1>
            <div class="flex space-x-2 sm:space-x-4">
                <a href="admin-create-election.php" class="text-white font-semibold py-2 px-4 rounded-lg bg-indigo-600">Create Election</a>
                <a href="admin.php" class="text-gray-400 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Dashboard</a>
                <a href="result.php" class="text-gray-400 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">View Results</a>
                <a href="logout.php" class="text-red-400 hover:text-red-600 font-semibold py-2 px-4 rounded-lg transition duration-200">Logout</a>
            </div>
        </div>
    </nav>
    <main class="flex-grow py-12">
        <div class="container mx-auto px-4 max-w-2xl">
            <h2 class="text-3xl font-bold text-center mb-6">Create New Election</h2>
            <div class="bg-white p-10 rounded-3xl shadow-lg">
                <?php if ($message) echo $message; ?>
                <form method="POST" id="createElectionForm">
                    <div class="mb-4">
                        <label for="election-title" class="block text-gray-700 font-semibold mb-2">Election Title</label>
                        <input type="text" id="election-title" name="title" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                    </div>
                    <div class="mb-4">
                        <label for="election-description" class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea id="election-description" name="description" rows="4" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="start-date" class="block text-gray-700 font-semibold mb-2">Start Date</label>
                            <input type="date" id="start-date" name="start_date" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                        </div>
                        <div>
                            <label for="end-date" class="block text-gray-700 font-semibold mb-2">End Date</label>
                            <input type="date" id="end-date" name="end_date" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200" required>
                        </div>
                    </div>
                    <button type="submit" id="submitButton" class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-full hover:bg-indigo-700 transition duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:transform-none disabled:cursor-not-allowed">
                        Create Election
                    </button>
                </form>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-lg mt-8">
                <h3 class="text-xl font-bold mb-4">All Elections</h3>
                <table class="w-full text-left table-auto border-collapse">
                    <thead><tr><th class="py-2 px-4 bg-gray-100">Title</th><th class="py-2 px-4 bg-gray-100">Start Date</th><th class="py-2 px-4 bg-gray-100">End Date</th></tr></thead>
                    <tbody>
                    <?php $result = $conn->query('SELECT title, start_date, end_date FROM elections ORDER BY start_date DESC');
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['start_date']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['end_date']) ?></td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="3">No elections found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
