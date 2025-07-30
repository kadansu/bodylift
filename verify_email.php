<?php 
require_once 'config.php';

$email = $_GET['email'] ?? '';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = trim($_POST['verification_code']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT verification_code, verification_expiry FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $errors[] = "User not found.";
    } elseif ($user['verification_code'] !== $input_code) {
        $errors[] = "Invalid verification code.";
    } elseif (strtotime($user['verification_expiry']) < time()) {
        $errors[] = "Verification code has expired.";
    } else {
        $update = $pdo->prepare("UPDATE users SET email_verified = 1, verification_code = NULL, verification_expiry = NULL WHERE email = ?");
        if ($update->execute([$email])) {
            
            header("Location: meal_plans.php");
            exit;
        } else {
            $errors[] = "Failed to verify email. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email - BodyLift</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <section class="container">
        <h1>Verify Your Email</h1>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php else: ?>
            <?php foreach ($errors as $err): ?>
                <p class="error-message"><?= $err ?></p>
            <?php endforeach; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="form-group">
                    <label for="verification_code">Verification Code</label>
                    <input type="text" name="verification_code" id="verification_code" required>
                </div>
                <button type="submit" class="btn btn-primary">Verify</button>
            </form>
        <?php endif; ?>
    </section>
</body>
</html>

