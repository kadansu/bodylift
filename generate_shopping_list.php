<?php
session_start();
require_once 'config.php';
require_once 'fpdf.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE user_id = ? ORDER BY week_number DESC");
$stmt->execute([$user_id]);
$meal_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate shopping list
$shopping_list = [];
foreach ($meal_plans as $plan) {
    $plan_data = json_decode($plan['plan_data'], true);
    foreach ($plan_data as $day => $day_data) {
        foreach ($day_data['meals'] as $meal => $meal_data) {
            foreach ($meal_data['foods'] as $food) {
                $food_name = $food['name'];
                if (!isset($shopping_list[$food_name])) {
                    $shopping_list[$food_name] = 0;
                }
                $shopping_list[$food_name] += 1;
            }
        }
    }
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, "Shopping List", 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Generated on: " . date("Y-m-d H:i:s"), 0, 1);
$pdf->Ln(5);

if (empty($shopping_list)) {
    $pdf->Cell(0, 10, "No items in your shopping list.", 0, 1);
} else {
    foreach ($shopping_list as $item => $qty) {
        $pdf->Cell(0, 8, "- $item (Qty: approx. $qty)", 0, 1);
    }
}

$pdf->Output('D', 'shopping_list.pdf');
exit;
