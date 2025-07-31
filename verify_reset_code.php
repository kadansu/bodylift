<?php
require_once 'config.php';

$email = $_GET['email'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND reset_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user && strtotime($user['reset_expires_at']) > time()) {
        header("Location: reset_password.php?email=" . urlencode($email));
        exit;
    } else {
        $errors[] = "Invalid or expired code.";
    }
}
?>

<!-- HTML form -->
<form method="POST">
    <input type="text" name="code" placeholder="Enter 6-digit code" required>
    <button type="submit">Verify Code</button>
    <?= implode('<br>', $errors); ?>
</form>
