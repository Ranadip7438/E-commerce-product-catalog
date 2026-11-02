
<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';

// If already logged in, go to homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$email = '';
$error = '';

// Check login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password == '') {
        $error = 'Please enter your email and password.';
    } else {

        // Find user by email
        $stmt = $mysqli->prepare(
            "SELECT id, name, email, password_hash
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check password
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect email or password.';
        } else {

            // Start user session
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Log in - Electro';
require 'includes/header.php';
?>

<section class="auth-page auth-page-login">

    <div class="auth-intro">
        <p class="eyebrow">WELCOME BACK</p>

        <h1>Your everyday<br>tech starts <span>here.</span></h1>

        <p>
            Log in to your Electro account and explore
            your favourite electronics.
        </p>

        <div class="auth-quote">
            "Good tech. Better living."
            <span>THE ELECTRO EDIT</span>
        </div>
    </div>

    <div class="auth-card">

        <p class="eyebrow">MEMBER ACCESS</p>
        <h2>Welcome back</h2>

        <p class="auth-subtitle">
            New to Electro?
            <a href="register.php">Create an account</a>
        </p>

        <?php if ($error != '') { ?>
            <div class="form-alert" role="alert">
                <?= e($error) ?>
            </div>
        <?php } ?>

        <form class="auth-form" action="login.php" method="post">

            <label for="email">Email address</label>

            <input
                id="email"
                name="email"
                type="email"
                required
                value="<?= e($email) ?>"
                placeholder="you@example.com"
            >

            <label for="password">Password</label>

            <input
                id="password"
                name="password"
                type="password"
                required
                placeholder="Your password"
            >

            <button class="button button-dark auth-submit" type="submit">
                Log in →
            </button>

        </form>

        
</section>

<?php require 'includes/footer.php'; ?>
