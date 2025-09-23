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
// Analytics
$analytics = [
    'total_users' => 0,
    'total_votes' => 0,
    'votes_per_candidate' => []
];
$result = $conn->query('SELECT COUNT(*) as total FROM users');
if ($result && $row = $result->fetch_assoc()) $analytics['total_users'] = $row['total'];
$result = $conn->query('SELECT COUNT(*) as total FROM votes');
if ($result && $row = $result->fetch_assoc()) $analytics['total_votes'] = $row['total'];
$result = $conn->query('SELECT c.name, COUNT(v.id) as votes FROM candidates c LEFT JOIN votes v ON c.id = v.candidate_id GROUP BY c.id, c.name');
if ($result) while ($row = $result->fetch_assoc()) $analytics['votes_per_candidate'][] = $row;
// Handle candidate add/delete
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
if (isset($_POST['delete_candidate_id'])) {
    $delete_id = intval($_POST['delete_candidate_id']);
    $stmt = $conn->prepare('DELETE FROM candidates WHERE id = ?');
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Candidate deleted successfully!</div>';
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error deleting candidate: ' . htmlspecialchars($stmt->error) . '</div>';
    }
    $stmt->close();
}
// Handle user deletion
if (isset($_POST['delete_user'])) {
    $del_user = trim($_POST['delete_user']);
    if ($del_user !== 'admin') {
        // Delete user's votes first
        $stmt = $conn->prepare('DELETE FROM votes WHERE username = ?');
        $stmt->bind_param('s', $del_user);
        $stmt->execute();
        $stmt->close();
        // Delete user
        $stmt = $conn->prepare('DELETE FROM users WHERE username = ?');
        $stmt->bind_param('s', $del_user);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">User deleted successfully!</div>';
        } else {
            $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error deleting user: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    }
}
// Handle reset votes
if (isset($_POST['reset_votes'])) {
    if ($conn->query('DELETE FROM votes')) {
        $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">All votes have been reset!</div>';
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error resetting votes.</div>';
    }
}
// Handle admin password change
if (isset($_POST['new_admin_password'])) {
    $newpass = $_POST['new_admin_password'];
    $hash = password_hash($newpass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = "admin"');
    $stmt->bind_param('s', $hash);
    if ($stmt->execute()) {
        $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Admin password changed!</div>';
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">Error changing password.</div>';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4 flex flex-col items-center">
    <div class="w-full max-w-6xl">
        <!-- Navigation -->
        <nav class="bg-gray-900 shadow-lg py-4 mb-8 rounded-b-3xl">
            <div class="container mx-auto px-6 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <span class="text-3xl font-extrabold text-white tracking-tight">Admin Panel</span>
                    <span class="ml-2 px-3 py-1 rounded-full bg-indigo-600 text-white text-xs font-semibold">Voting System</span>
                </div>
                <div class="flex gap-2">
                    <a href="admin.php" class="text-gray-300 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Dashboard</a>
                    <a href="add-candidate.php" class="text-gray-300 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Add Candidate</a>
                    <a href="results.php" class="text-gray-300 hover:text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Results</a>
                    <a href="logout.php" class="text-red-400 hover:text-red-600 font-semibold py-2 px-4 rounded-lg transition duration-200">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Main Content Wrapper -->
        <div class="w-full bg-white p-8 rounded-3xl shadow-2xl border border-gray-200">
            <h1 class="text-4xl font-extrabold mb-10 text-center text-gray-800 flex items-center justify-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-600 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">Admin Dashboard</span>
            </h1>
            
            <?php if (!empty($message)) echo $message; ?>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Analytics Section -->
                <div class="lg:col-span-2 p-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-inner border border-gray-200">
                    <h2 class="text-2xl font-semibold mb-6 text-gray-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                        </svg>
                        Analytics
                    </h2>
                    <div class="grid md:grid-cols-2 gap-6 text-gray-600">
                        <div id="total-users" class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-lg">Total Users: <span class="font-bold text-indigo-700 text-2xl ml-2"></span></div>
                        <div id="total-votes" class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-lg">Total Votes: <span class="font-bold text-indigo-700 text-2xl ml-2"></span></div>
                    </div>
                </div>
                
                <!-- Poll Results Section -->
                <div class="p-8 bg-slate-50 rounded-[1.5rem] shadow-inner border border-gray-200">
                    <h2 class="text-2xl font-semibold mb-6 text-indigo-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m-9-5a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6zm3-2a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-6zm3-2a2 2 0 00-2-2h2a2 2 0 012 2v6a2 2 0 01-2 2h-2a2 2 0 01-2-2v-6z" />
                        </svg>
                        Poll Results
                    </h2>
                    <div class="space-y-4" id="poll-results"></div>
                </div>
            </div>

            <!-- Action & Management Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8">
                <!-- Add Candidate Section -->
                <div class="p-8 bg-slate-50 rounded-[1.5rem] shadow-inner border border-gray-200 transition-all duration-300 hover:shadow-lg">
                    <h2 class="text-2xl font-semibold mb-6 text-indigo-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Add New Candidate
                    </h2>
                    <form method="POST" action="">
                        <div class="flex flex-col gap-4">
                            <label for="name" class="block text-gray-700 font-medium">Candidate Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200" required>
                            <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-indigo-700 transition duration-300 shadow-md transform hover:scale-105">Add Candidate</button>
                        </div>
                    </form>
                </div>

                <!-- Management Actions Section -->
                <div class="p-8 bg-slate-50 rounded-[1.5rem] shadow-inner border border-gray-200 transition-all duration-300 hover:shadow-lg">
                    <h2 class="text-2xl font-semibold mb-6 text-indigo-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Management Actions
                    </h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-700 mb-2">Reset All Votes</h3>
                            <form method="POST" action="" onsubmit="return showConfirm('WARNING: This will permanently delete all votes. Are you sure?');">
                                <input type="hidden" name="reset_votes" value="1">
                                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition duration-300 shadow-md transform hover:scale-105">Reset Votes</button>
                            </form>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-700 mb-2">Change Admin Password</h3>
                            <form method="POST" action="">
                                <div class="flex flex-col gap-4">
                                    <input type="hidden" name="change_admin_password" value="1">
                                    <input type="password" id="new_admin_password" name="new_admin_password" placeholder="New Password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200" required>
                                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-indigo-700 transition duration-300 shadow-md transform hover:scale-105">Change</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Candidate and User Tables -->
                <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <!-- List Candidates Section (with sample data) -->
                    <div class="p-8 bg-slate-50 rounded-[1.5rem] shadow-inner border border-gray-200 transition-all duration-300 hover:shadow-lg">
                        <h2 class="text-2xl font-semibold mb-6 text-indigo-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 01-4 4H6a4 4 0 01-4-4V4a4 4 0 014-4h3a4 4 0 014 4v3z" />
                            </svg>
                            All Candidates
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left table-auto border-collapse rounded-xl overflow-hidden shadow-md">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="py-3 px-4 text-gray-800 rounded-tl-xl">Name</th>
                                        <th class="py-3 px-4 text-gray-800 rounded-tr-xl">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $result = $conn->query('SELECT id, name FROM candidates');
                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()): ?>
                                        <tr class="border-t border-gray-200 hover:bg-gray-100 transition duration-200">
                                            <td class="py4 px4 - admin.php:242"><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td class="py-4 px-4">
                                                <form method="POST" action="" class="inline-block" onsubmit="return showConfirm('Are you sure you want to delete this candidate?');">
                                                    <input type="hidden - admin.php:245" name="delete_candidate_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded-full transition duration-300 shadow-sm transform hover:scale-105">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr><td colspan="2">No candidates found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                    <!-- Manage Users Section (with sample data) -->
                    <div class="p-8 bg-slate-50 rounded-[1.5rem] shadow-inner border border-gray-200 transition-all duration-300 hover:shadow-lg">
                        <h2 class="text-2xl font-semibold mb-6 text-gray-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 01-4 4H6a4 4 0 01-4-4V4a4 4 0 014-4h3a4 4 0 014 4v3z" />
                            </svg>
                            All Registered Users
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full mb-4 text-left table-auto border-collapse rounded-xl overflow-hidden shadow-md">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="py-3 px-4 text-gray-800 rounded-tl-xl">Username</th>
                                        <th class="py-3 px-4 text-gray-800 rounded-tr-xl">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $result = $conn->query('SELECT username FROM users');
                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()): ?>
                                        <tr class="border-t border-gray-200 hover:bg-gray-100 transition duration-200">
                                            <td class="py4 px4 - admin.php:280"><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td class="py-4 px-4">
                                                <?php if ($row['username'] !== 'admin'): ?>
                                                <form method="POST - admin.php:283" action="" class="inline-block" onsubmit="return showConfirm('Are you sure you want to delete the user <?php echo htmlspecialchars($row['username']); ?>?');">
                                                    <input type="hidden - admin.php:284" name="delete_user" value="<?php echo htmlspecialchars($row['username']); ?>">
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded-full transition duration-300 shadow-sm transform hover:scale-105">Delete</button>
                                                </form>
                                                <?php else: ?>
                                                    <span class="text-gray-400">Admin</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr><td colspan="2">No users found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Custom Modal Confirmation Dialog -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50" style="display:none;">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-xl w-full max-w-md">
            <p id="confirmMessage" class="mb-6 text-gray-800 dark:text-gray-100 font-semibold text-lg"></p>
            <div class="flex justify-center space-x-4">
                <button id="confirmYes" class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-full transition duration-300 shadow-sm">Yes</button>
                <button id="confirmNo" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-6 rounded-full transition duration-300 shadow-sm">No</button>
            </div>
        </div>
    </div>

    <script>
        function showConfirm(message) {
            return new Promise((resolve) => {
                const modal = document.getElementById('confirmModal');
                const confirmMessage = document.getElementById('confirmMessage');
                const confirmYes = document.getElementById('confirmYes');
                const confirmNo = document.getElementById('confirmNo');
                confirmMessage.textContent = message;
                modal.style.display = 'flex';
                confirmYes.onclick = () => {
                    modal.style.display = 'none';
                    resolve(true);
                };
                confirmNo.onclick = () => {
                    modal.style.display = 'none';
                    resolve(false);
                };
            });
        }
        // Intercept form submissions to use the custom confirmation
        document.querySelectorAll('form[onsubmit*="showConfirm"]').forEach(form => {
            const originalOnsubmit = form.onsubmit;
            form.onsubmit = async (event) => {
                event.preventDefault();
                const msgMatch = form.getAttribute('onsubmit').match(/showConfirm\('(.*?)'\)/);
                const result = await showConfirm(msgMatch ? msgMatch[1] : 'Are you sure?');
                if (result) form.submit();
            };
        });
        // Dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.createElement('button');
            toggleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71M21 12h1M2 12H1m16.24 7.07l-.71-.71M7.05 19.07l-.71-.71" /></svg>';
            toggleBtn.className = 'ml-4 bg-gray-800 text-white rounded-full p-2 hover:bg-gray-700 transition duration-200';
            toggleBtn.title = 'Toggle dark mode';
            toggleBtn.onclick = function() {
                document.documentElement.classList.toggle('dark');
            };
            document.querySelector('nav .container').appendChild(toggleBtn);
        });
    </script>
</body>
<script>
function updateAnalytics() {
    fetch('admin-analytics.php')
        .then(res => res.json())
        .then(data => {
            document.querySelector('#total-users span').textContent = data.total_users;
            document.querySelector('#total-votes span').textContent = data.total_votes;
            // Poll results
            let html = '';
            let totalVotes = data.total_votes > 0 ? data.total_votes : 1;
            data.votes_per_candidate.forEach(row => {
                let percent = Math.round((row.votes / totalVotes) * 1000) / 10;
                html += `<div class="rounded-lg bg-white p-4 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 font-medium">${row.name}</span>
                        <span class="text-indigo-600 font-bold">${row.votes} votes (${percent}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: ${percent}%"></div>
                    </div>
                </div>`;
            });
            document.getElementById('poll-results').innerHTML = html;
        });
}
updateAnalytics();
setInterval(updateAnalytics, 5000);
</script>
</body>
</html>