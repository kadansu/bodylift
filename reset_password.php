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
        echo "<div class='success-message'>Password reset successful. <a href='login.php'>Login</a></div>";
        exit;
    }
}
?>

<!-- HTML form -->
<div class="reset-wrapper">
    <form method="POST" class="reset-form">
        <h2>Reset Your Password</h2>

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required>
        </div>

        <div class="form-group">
            <label for="confirm">Confirm Password</label>
            <input type="password" name="confirm" id="confirm" placeholder="Confirm new password" required>
        </div>

        <button type="submit">Reset Password</button>

        <?php if (!empty($errors)): ?>
            <div class="error-message"><?= implode('<br>', $errors); ?></div>
        <?php endif; ?>
    </form>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #1a1a1a;
        color: #fff;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .reset-wrapper {
        width: 100%;
        max-width: 500px;
        padding: 2rem;
        background-color: #2d2d2d;
        border: 1px solid #ffb300;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .reset-form h2 {
        text-align: center;
        color: #ffb300;
        margin-bottom: 2rem;
        text-transform: uppercase;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #ffb300;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        background-color: #2d2d2d;
        border: 2px solid #ffb300;
        border-radius: 5px;
        color: #fff;
        font-size: 1rem;
    }

    .form-group input:focus {
        border-color: #ff9800;
        outline: none;
        box-shadow: 0 0 5px rgba(255, 152, 0, 0.5);
    }

    button {
        width: 100%;
        padding: 0.75rem;
        background-color: #ffb300;
        color: #1a1a1a;
        border: none;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: #ff9800;
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(255, 179, 0, 0.5);
    }

    .error-message {
        margin-top: 1rem;
        color: #ff4444;
        font-weight: bold;
        text-align: center;
    }

    .success-message {
        margin-top: 1rem;
        color: #ffb300;
        font-weight: bold;
        text-align: center;
    }

    @media (max-width: 600px) {
        .reset-wrapper {
            padding: 1.5rem;
        }
    }
</style>
