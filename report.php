<?php
session_start();
require_once 'config.php';
require_once 'fpdf.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (!isset($_GET['plan_id'])) {
    die("Missing plan ID.");
}

$user_id = $_SESSION['user_id'];
$plan_id = $_GET['plan_id'];

// Fetch meal plan
$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $user_id]);
$plan = $stmt->fetch();

if (!$plan) {
    die("Plan not found.");
}

$plan_data = json_decode($plan['plan_data'], true);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header
$pdf->Cell(0, 10, "Meal Plan Report - Week " . $plan['week_number'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Generated on: " . $plan['created_at'], 0, 1);

foreach ($plan_data as $day => $day_data) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $day, 0, 1);

    foreach ($day_data['meals'] as $meal => $meal_data) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, ucfirst($meal), 0, 1);

        $pdf->SetFont('Arial', '', 11);
        foreach ($meal_data['foods'] as $food) {
            $pdf->Cell(0, 6, "- " . $food['name'] . " ({$food['calories']} kcal)", 0, 1);
        }

        $pdf->Cell(0, 6, "Total Calories: " . round($meal_data['calories'], 2) . " kcal", 0, 1);
        $pdf->Cell(0, 6, "Macros - Carbs: " . round($meal_data['macros']['carbs'], 2) . "g, Protein: " . round($meal_data['macros']['protein'], 2) . "g, Fat: " . round($meal_data['macros']['fat'], 2) . "g", 0, 1);
        $pdf->Ln(2);
    }

    $pdf->Ln(4);
}

$pdf->Output('D', 'meal_plan_week_' . $plan['week_number'] . '.pdf');
exit;
?>
