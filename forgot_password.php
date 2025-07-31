<?php
require_once 'config.php';
require_once 'mail.php';

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

            send_verification_email($email, $code);

            header("Location: verify_reset_code.php?email=" . urlencode($email));
            exit;
        } else {
            $errors[] = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - NutriLift</title>
    <style>
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
            padding: 2rem;
        }

        .reset-container {
            background-color: #2d2d2d;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #ffb300;
        }

        .reset-container h2 {
            color: #ffb300;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
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
            padding: 0.75rem;
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
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #ff9800;
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255, 179, 0, 0.5);
        }

        .error-message {
            color: #ff4444;
            margin-top: 1rem;
            text-align: center;
            font-weight: bold;
        }

        .success-message {
            color: #ffb300;
            margin-top: 1rem;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .reset-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Your Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" name="email" id="email" required>
            </div>
            <button type="submit">Send Reset Code</button>
        </form>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?= implode('<br>', $errors); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
