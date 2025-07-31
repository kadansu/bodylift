<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_weight'])) {
    $weight = $_POST['weight'];
    $stmt = $pdo->prepare("INSERT INTO progress (user_id, weight) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $weight])) {
        $success_message = "Weight entry added successfully!";
    } else {
        $error_message = "Failed to add weight entry.";
    }
}

if (isset($_GET['delete'])) {
    $entry_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM progress WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$entry_id, $user_id])) {
        $success_message = "Weight entry deleted successfully!";
    } else {
        $error_message = "Failed to delete weight entry.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM progress WHERE user_id = ? ORDER BY recorded_at ASC");
$stmt->execute([$user_id]);
$progress_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$weights = [];
foreach ($progress_entries as $entry) {
    $dates[] = $entry['recorded_at'];
    $weights[] = $entry['weight'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracker - NutriLift</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="container">
            <h1>Your Progress</h1>
            <p>Track your weight gain journey below.</p>
        </div>
    </section>

    <main class="container">
        <section>
            <h2>Add Weight Entry</h2>
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="weight">Current Weight (kg)</label>
                    <input type="number" step="0.1" id="weight" name="weight" required>
                </div>
                <button type="submit" name="add_weight" class="btn btn-primary">Add Entry</button>
            </form>

            <h2>Your Progress</h2>
            <?php if (!empty($success_message)) echo "<p class='success-message'>$success_message</p>"; ?>
            <?php if (!empty($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>

            <?php if (empty($progress_entries)): ?>
                <p>No progress entries yet. Add your weight above to get started.</p>
            <?php else: ?>
                <canvas id="progressChart" width="600" height="300"></canvas>

                <div class="progress-grid">
                    <?php foreach ($progress_entries as $entry): ?>
                        <div class="progress-entry">
                            <h3>Weight: <?php echo htmlspecialchars($entry['weight']); ?> kg</h3>
                            <p><strong>Recorded On:</strong> <?php echo htmlspecialchars($entry['recorded_at']); ?></p>
                            <a href="progress.php?delete=<?php echo $entry['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Weight (kg)',
                    data: <?php echo json_encode($weights); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: '#00c853',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Weight (kg)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
