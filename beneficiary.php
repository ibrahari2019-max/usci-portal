<?php
/**
 * beneficiary.php – Public Beneficiary Registration Form with WOW animation
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/auth_handler.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        // Basic sanitization & validation
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $email      = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone      = trim($_POST['phone'] ?? '');
        $address    = trim($_POST['address'] ?? '');
        $city       = trim($_POST['city'] ?? '');
        $state      = trim($_POST['state'] ?? '');
        $zip        = trim($_POST['zip'] ?? '');

        if (!$first_name || !$last_name || !$email) {
            $errors[] = 'First name, last name and a valid email are required.';
        }

        if (empty($errors)) {
            $data = [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'phone'      => $phone,
                'address'    => $address,
                'city'       => $city,
                'state'      => $state,
                'zip'        => $zip,
            ];
            if (register_beneficiary($data)) {
                $success = true;
            } else {
                $errors[] = 'Failed to register. Please try again later.';
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
    <title>Register as Beneficiary – UCSI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/wow.js" defer></script>
</head>
<body>
    <header class="wow">
        <h1>Register as Beneficiary</h1>
        <p>Building stronger community together</p>
        <a href="index.php" class="btn">Back to Home</a>
    </header>

    <section class="container">
        <?php if ($success): ?>
            <div class="wow" style="background:#e0ffe0;padding:1rem;border-radius:4px;margin-top:1rem;">
                <p>Thank you! Your registration was successful.</p>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="wow" style="background:#ffe0e0;padding:1rem;border-radius:4px;margin-top:1rem;">
                    <?php foreach ($errors as $e): ?>
                        <p style="color:#c00;">&bull; <?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form id="beneficiary-form" class="wow" method="post" action="beneficiary.php" style="margin-top:1rem;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">

                <label for="address">Address</label>
                <input type="text" id="address" name="address">

                <label for="city">City</label>
                <input type="text" id="city" name="city">

                <label for="state">State</label>
                <input type="text" id="state" name="state">

                <label for="zip">ZIP / Postal Code</label>
                <input type="text" id="zip" name="zip">

                <button type="submit" name="register" class="btn" style="margin-top:1rem;">Submit Registration</button>
            </form>
        <?php endif; ?>
    </section>

    <footer class="wow">
        <p>&copy; <?php echo date('Y'); ?> UNITED COMMUNITY SUPPORT INITIATIVE</p>
    </footer>
</body>
</html>
