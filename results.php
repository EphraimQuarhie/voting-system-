<?php
include 'db.php';
session_start();
// Only allow logged in users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow-xl border border-gray-200 mt-10">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-700">Poll Results</h1>
        <canvas id="resultsChart" height="120"></canvas>
        <div id="poll-results" class="mt-8">
            <?php
            $result = $conn->query('SELECT c.name, c.position, COUNT(v.id) as votes FROM candidates c LEFT JOIN votes v ON c.id = v.candidate_id GROUP BY c.id, c.name, c.position');
            $totalVotes = 0;
            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                    $totalVotes += $row['votes'];
                }
            }
            if ($rows) {
                echo '<table class="w-full text-left table-auto border-collapse rounded-xl overflow-hidden shadow-md mt-6">';
                echo '<thead><tr class="bg-gray-200"><th class="py-3 px-4 text-gray-800">Name</th><th class="py-3 px-4 text-gray-800">Position</th><th class="py-3 px-4 text-gray-800">Votes</th><th class="py-3 px-4 text-gray-800">% of Total</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $percent = $totalVotes > 0 ? round(($row['votes'] / $totalVotes) * 1000) / 10 : 0;
                    echo '<tr class="border-t border-gray-200 hover:bg-gray-100 transition duration-200">';
                    echo '<td class="py-2 px-4">'.htmlspecialchars($row['name']).'</td>';
                    echo '<td class="py-2 px-4">'.htmlspecialchars($row['position']).'</td>';
                    echo '<td class="py-2 px-4">'.$row['votes'].'</td>';
                    echo '<td class="py-2 px-4">'.$percent.'%</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="text-gray-500">No poll results available.</div>';
            }
            ?>
        </div>
        <a href="vote.php" class="block mt-8 text-center text-indigo-600 hover:underline">Back to Voting</a>
    </div>
    <script>
    // Chart.js bar chart for results
    <?php
    $labels = [];
    $votes = [];
    $colors = [];
    foreach ($rows as $row) {
        $labels[] = htmlspecialchars($row['name'].' ('.$row['position'].')');
        $votes[] = (int)$row['votes'];
        $colors[] = "'rgba(".rand(50,200).",".rand(50,200).",".rand(100,255).",0.7)'";
    }
    ?>
    const ctx = document.getElementById('resultsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php echo implode(',', array_map(function($l){return "'".addslashes($l)."'";}, $labels)); ?>],
            datasets: [{
                label: 'Votes',
                data: [<?php echo implode(',', $votes); ?>],
                backgroundColor: [<?php echo implode(',', $colors); ?>],
                borderColor: 'rgba(99,102,241,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Votes per Candidate', color: '#3730a3', font: { size: 18 } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#6366f1' } },
                x: { ticks: { color: '#6366f1' } }
            }
        }
    });
    </script>
</body>
</html>
