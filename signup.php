<?php
require_once 'config.php';
require_once 'mail.php';

$success_message = '';
$errors = [];

$first_name = '';
$last_name = '';
$email = '';
$age = '';
$weight_goal = '';
$dietary_preferences = '';
$allergies = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $age = $_POST['age'];
    $weight_goal = $_POST['weight_goal'];
    $dietary_preferences = $_POST['dietary_preferences'];
    $allergies = $_POST['allergies'];

    if (!preg_match("/^[a-zA-Z\s'-]+$/", $first_name)) {
        $errors['first_name'] = "Only letters, spaces, apostrophes, and hyphens are allowed.";
    }
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $last_name)) {
        $errors['last_name'] = "Only letters, spaces, apostrophes, and hyphens are allowed.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (!is_numeric($age) || $age < 15) {
        $errors['age'] = "You must be at least 15 years old.";
    }
    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors['email'] = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $code = rand(100000, 999999);
            $expiry = date('Y-m-d H:i:s', time() + 600); 

            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, password, age, weight_goal, dietary_preferences, allergies, verification_code, verification_expiry)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $first_name, $last_name, $email, $hashed_password, $age,
                $weight_goal, $dietary_preferences, $allergies,
                $code, $expiry
            ]);

            if ($success) {
                $subject = "Bodylift Email Verification";
                $message = "Your verification code is: $code. It expires in 10 minutes.";
                $headers = "From: BodyLift <no-reply@bodylift.com>";

                mail($email, $subject, $message, $headers);
                header("Location: verify_email.php?email=" . urlencode($email));
                exit;
            } else {
                $errors['general'] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up -Bodylift </title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<section class="hero">
    <div class="container">
        <h1>Sign Up for Bodylift </h1>
        <p>Create your account to start your healthy weight gain journey.</p>
    </div>
</section>
<main class="container">
    <section class="signup-form">
        <h2>Sign Up</h2>
        <?php if ($success_message): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if (!empty($errors['general'])): ?>
            <p class="error-message"><?= $errors['general'] ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-flex">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                    <div class="error-message"><?= $errors['first_name'] ?? '' ?></div>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                    <div class="error-message"><?= $errors['last_name'] ?? '' ?></div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div class="error-message"><?= $errors['password'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <div class="error-message" id="email-error"><?= $errors['email'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="15" value="<?= htmlspecialchars($age) ?>" required>
                <div class="error-message" id="age-error"><?= $errors['age'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="weight_goal">Weight Goal (kg)</label>
                <input type="number" step="0.1" id="weight_goal" name="weight_goal" value="<?= htmlspecialchars($weight_goal) ?>" required>
            </div>
            <div class="form-group">
                <label for="dietary_preferences">Dietary Preferences</label>
                <textarea id="dietary_preferences" name="dietary_preferences"><?= htmlspecialchars($dietary_preferences) ?></textarea>
            </div>
            <div class="form-group">
                <label for="allergies">Allergies</label>
                <textarea id="allergies" name="allergies"><?= htmlspecialchars($allergies) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php" style="background-color:#ffb300; color: white; padding: 2px 6px; border-radius: 2px; text-decoration: none;">login</a></p>
    </section>
</main>
<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
