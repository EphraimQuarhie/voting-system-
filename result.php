<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
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
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Admin Navbar -->
    <nav class="bg-gray-800 shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Admin Panel</h1>
            <div class="flex space-x-2 sm:space-x-4">
                <a href="admin-create-election.php" class="text-gray-400 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Create Election</a>
                <a href="admin-add-candidate.php" class="text-gray-400 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Add Candidate</a>
                <a href="result.php" class="text-white font-semibold py-2 px-4 rounded-lg bg-indigo-600">View Results</a>
                <a href="logout.php" class="text-red-400 hover:text-red-600 font-semibold py-2 px-4 rounded-lg transition duration-200">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow py-12">
        <div class="container mx-auto px-4 max-w-2xl">
            <h2 class="text-3xl font-bold text-center mb-6">Election Results</h2>
            <div class="bg-white p-10 rounded-3xl shadow-lg overflow-x-auto">
                <table class="w-full text-left table-auto border-collapse">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 bg-gray-100 rounded-tl-xl font-bold text-gray-600">Candidate</th>
                            <th class="py-3 px-4 bg-gray-100 font-bold text-gray-600">Total Votes</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-500 divide-y divide-gray-200">
                        <?php
                        $sql = 'SELECT c.name, COUNT(v.id) AS votes
                                FROM candidates c
                                LEFT JOIN votes v ON c.id = v.candidate_id
                                GROUP BY c.id, c.name
                                ORDER BY votes DESC, c.name ASC';
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="py-4 px-4 align-middle font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="py-4 px-4 align-middle text-lg text-indigo-700 font-bold"><?= (int)$row['votes'] ?></td>
                                </tr>
                        <?php endwhile;
                        else: ?>
                            <tr><td colspan="2" class="py-4 px-4 text-center text-gray-400">No candidates or votes yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>


</body>
</html>
