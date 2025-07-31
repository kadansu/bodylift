<?php
session_start();
require_once 'config.php';
require_once 'fpdf.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Fetch weight progress data
$stmt = $pdo->prepare("SELECT * FROM progress WHERE user_id = ? ORDER BY recorded_at DESC");
$stmt->execute([$user_id]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Weight Progress Tracker', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1);
$pdf->Ln(5);

if (empty($entries)) {
    $pdf->Cell(0, 10, 'No progress entries available.', 0, 1);
} else {
    foreach ($entries as $entry) {
        $weight = htmlspecialchars($entry['weight']);
        $date = htmlspecialchars($entry['recorded_at']);
        $pdf->Cell(0, 8, "- $date: $weight kg", 0, 1);
    }
}

$pdf->Output('D', 'progress_tracker.pdf');
exit;
