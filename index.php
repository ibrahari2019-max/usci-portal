<?php
/**
 * index.php – Public landing page with Admin login modal
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/auth_handler.php';

// Process admin login if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $login_error = 'Invalid CSRF token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (login_admin($email, $password)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $login_error = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNITED COMMUNITY SUPPORT INITIATIVE</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/wow.js" defer></script>
</head>
<body>
    <header class="wow">
        <h1>UNITED COMMUNITY SUPPORT INITIATIVE</h1>
        <p>Building stronger community together</p>
        <button id="open-admin-modal" class="btn">Admin Login</button>
        <a href="beneficiary.php" class="btn">Register as Beneficiary</a>
    </header>

    <section class="container">
        <div class="row">
            <div class="col wow">
                <h2>Our Mission</h2>
                <p>We empower communities by providing resources and support to those in need.</p>
            </div>
            <div class="col wow">
                <h2>Get Involved</h2>
                <p>Join our volunteers, donate, or become a beneficiary.</p>
            </div>
        </div>
    </section>

    <!-- Admin Login Modal -->
    <div id="admin-modal" class="modal">
        <div class="modal-content">
            <button class="close">&times;</button>
            <h2>Admin Login</h2>
            <?php if (!empty($login_error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>
            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="admin_login" class="btn">Login</button>
            </form>
        </div>
    </div>

    <footer class="wow">
        <p>&copy; <?php echo date('Y'); ?> UNITED COMMUNITY SUPPORT INITIATIVE</p>
    </footer>
</body>
</html>
