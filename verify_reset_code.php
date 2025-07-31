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
<div class="verify-container">
    <form method="POST" class="verify-form">
        <h2>Verify Reset Code</h2>
        <div class="form-group">
            <label for="code">Enter 6-digit code</label>
            <input type="text" name="code" id="code" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify Code</button>

        <?php if (!empty($errors)): ?>
            <div class="error-message"><?= implode('<br>', $errors); ?></div>
        <?php endif; ?>
    </form>
</div>

<style>
    /* Base Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.6;
        background-color: #1a1a1a;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 1rem;
    }

    .verify-container {
        width: 100%;
        max-width: 500px;
    }

    .verify-form {
        background-color: #2d2d2d;
        padding: 2rem;
        border-radius: 10px;
        border: 1px solid #ffb300;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .verify-form h2 {
        color: #ffb300;
        text-transform: uppercase;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #ffb300;
    }

    .form-group input {
        width: 100%;
        padding: 0.7rem;
        border: 2px solid #ffb300;
        border-radius: 5px;
        background-color: #2d2d2d;
        color: #fff;
        font-size: 1rem;
    }

    .form-group input:focus {
        border-color: #ff9800;
        outline: none;
        box-shadow: 0 0 5px rgba(255, 152, 0, 0.5);
    }

    .btn {
        display: inline-block;
        padding: 0.7rem 1.5rem;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        width: 100%;
        text-align: center;
    }

    .btn-primary {
        background-color: #ffb300;
        color: #1a1a1a;
    }

    .btn-primary:hover {
        background-color: #ff9800;
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(255, 179, 0, 0.5);
    }

    .error-message {
        color: #ff4444;
        margin-top: 1rem;
        font-weight: bold;
        text-align: center;
    }
</style>
