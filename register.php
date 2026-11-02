
<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

// If user is already logged in, go to homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$name = '';
$email = '';
$errors = [];

// Check registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validate name
    if (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = 'Name must be between 2 and 100 characters.';
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Validate password
    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check whether email already exists
    if (empty($errors)) {

        $stmt = $mysqli->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'This email is already registered.';
        } else {

            // Hash password before saving
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare(
                "INSERT INTO users (name, email, password_hash)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param("sss", $name, $email, $passwordHash);

            if ($stmt->execute()) {

                // Log in the new user
                session_regenerate_id(true);

                $_SESSION['user_id'] = $mysqli->insert_id;
                $_SESSION['user_name'] = $name;

                header('Location: index.php');
                exit;

            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Create Account - Electro';
require 'includes/header.php';
?>

<section class="auth-page">

    <div class="auth-intro">
        <p class="eyebrow">YOUR TECH, YOUR WAY</p>

        <h1>A little closer<br>to your <span>next favourite.</span></h1>

        <p>
            Create an account to explore your favourite
            electronic products.
        </p>

        <div class="auth-benefit">
            <span>01</span>
            <div>
                <strong>Simple account</strong>
                <small>One email, one account.</small>
            </div>
        </div>

        <div class="auth-benefit">
            <span>02</span>
            <div>
                <strong>Your own space</strong>
                <small>Discover electronics with Electro.</small>
            </div>
        </div>
    </div>

    <div class="auth-card">

        <p class="eyebrow">GET STARTED</p>
        <h2>Create your account</h2>

        <p class="auth-subtitle">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>

        <?php if (!empty($errors)) { ?>
            <div class="form-alert" role="alert">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?= e($error) ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <form class="auth-form" action="register.php" method="post">

            <label for="name">Full name</label>
            <input
                id="name"
                name="name"
                type="text"
                maxlength="100"
                required
                value="<?= e($name) ?>"
                placeholder="Your name"
            >

            <label for="email">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                maxlength="254"
                required
                value="<?= e($email) ?>"
                placeholder="you@example.com"
            >

            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                minlength="8"
                required
                placeholder="At least 8 characters"
            >

            <label for="confirm_password">Confirm password</label>
            <input
                id="confirm_password"
                name="confirm_password"
                type="password"
                minlength="8"
                required
                placeholder="Enter password again"
            >

            <button class="button button-dark auth-submit" type="submit">
                Create account →
            </button>

        </form>

</section>

<?php require 'includes/footer.php'; ?>
