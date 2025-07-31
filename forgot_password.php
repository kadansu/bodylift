<?php
require_once 'config.php';
require_once 'mail.php'; // ensure this sends emails properly

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $code = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $stmt = $pdo->prepare("UPDATE users SET reset_code = ?, reset_expires_at = ? WHERE email = ?");
            $stmt->execute([$code, $expires, $email]);

            send_verification_email($email, $code); // send 2FA code

            header("Location: verify_reset_code.php?email=" . urlencode($email));
            exit;
        } else {
            $errors[] = "No account found with that email.";
        }
    }
}
?>

<!-- HTML form -->
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Code</button>
    <?= implode('<br>', $errors); ?>
</form>
