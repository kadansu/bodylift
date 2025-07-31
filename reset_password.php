<?php
require_once 'config.php';

$email = $_GET['email'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_expires_at = NULL WHERE email = ?");
        $stmt->execute([$hashed, $email]);
        echo "Password reset successful. <a href='login.php'>Login</a>";
        exit;
    }
}
?>

<!-- HTML form -->
<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
    <?= implode('<br>', $errors); ?>
</form>
