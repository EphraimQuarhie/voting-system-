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
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
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
header('Content-Type: application/json');
echo json_encode($analytics);
