<?php include 'db.php'; ?>
<!DOCTYPE html>
<?php
include 'db.php';
session_start();
// Check if user is logged in (assumes user_id is stored in session after login)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$message = '';
// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = intval($_POST['candidate_id']);
    // Check if user has already voted
    $checkVote = $conn->prepare('SELECT id FROM votes WHERE user_id = ?');
    $checkVote->bind_param('i', $user_id);
    $checkVote->execute();
    $checkVote->store_result();
    if ($checkVote->num_rows > 0) {
        $message = '<div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-4">You have already voted.</div>';
    } else {
        $stmt = $conn->prepare('INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $user_id, $candidate_id);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Your vote has been recorded. Thank you!</div>';
        } else {
            $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    }
    $checkVote->close();
}
// Fetch candidates
$candidates = [];
$result = $conn->query('SELECT id, name FROM candidates');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Voting System</title>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Tailwind: bg-slate-50 */
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="#" class="text-xl font-bold text-gray-800">Voting System</a>
            <div class="flex space-x-4">
                <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">About</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">FAQ</a>
                <a href="logout.php" class="text-red-600 hover:text-red-800 font-semibold transition-colors duration-200">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-10 px-4">
        <div class="container mx-auto max-w-5xl">
            <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-2">Cast Your Vote</h1>
            <p class="text-center text-gray-500 mb-10">Select your preferred candidate by clicking the vote button below their profile.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Candidate 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="flex flex-col items-center">
                        <img src="https://placehold.co/150x150/f1f5f9/64748b?text=A" alt="Candidate A" class="w-32 h-32 rounded-full mb-4 ring-4 ring-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800">Alex Johnson</h2>
                        <p class="text-center text-gray-500 mt-1 mb-4">A visionary leader focused on sustainability.</p>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:scale-105">
                            Vote
                        </button>
                    </div>
                </div>

                <!-- Candidate 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="flex flex-col items-center">
                        <img src="https://placehold.co/150x150/f1f5f9/64748b?text=B" alt="Candidate B" class="w-32 h-32 rounded-full mb-4 ring-4 ring-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800">Maria Rodriguez</h2>
                        <p class="text-center text-gray-500 mt-1 mb-4">An experienced advocate for economic reform.</p>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:scale-105">
                            Vote
                        </button>
                    </div>
                </div>

                <!-- Candidate 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="flex flex-col items-center">
                        <img src="https://placehold.co/150x150/f1f5f9/64748b?text=C" alt="Candidate C" class="w-32 h-32 rounded-full mb-4 ring-4 ring-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800">Samuel Lee</h2>
                        <p class="text-center text-gray-500 mt-1 mb-4">A community organizer committed to social justice.</p>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:scale-105">
                            Vote
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <div class="container mx-auto px-6">
            <p>&copy; 2024 Online Voting System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
