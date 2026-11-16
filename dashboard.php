<?php
/**
 * dashboard.php – Admin command center (single page app)
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/auth_handler.php';
require_login(); // ensure admin is logged in

// Handle actions: logout, delete, export CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid CSRF token.';
        } else {
            switch ($_POST['action']) {
                case 'logout':
                    logout_admin();
                    break;
                case 'delete':
                    $id = (int)($_POST['beneficiary_id'] ?? 0);
                    if ($id > 0) {
                        delete_beneficiary($id);
                    }
                    break;
                case 'export':
                    $csv = export_beneficiaries_csv();
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="beneficiaries.csv"');
                    echo $csv;
                    exit();
            }
        }
    }
}

$beneficiaries = get_all_beneficiaries();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – UCSI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/wow.js" defer></script>
</head>
<body>
    <header class="wow">
        <h1>Admin Dashboard</h1>
        <form method="post" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <button type="submit" name="action" value="logout" class="btn">Logout</button>
        </form>
    </header>

    <section class="container">
        <div class="row">
            <div class="col">
                <h2>Beneficiaries</h2>
                <input type="text" id="beneficiary-search" placeholder="Search..." class="btn" style="margin-bottom:1rem;">
                <form method="post" style="margin-bottom:1rem;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" name="action" value="export" class="btn">Export CSV</button>
                </form>
                <table id="beneficiaries-table" class="wow">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($beneficiaries as $b): ?>
                        <tr>
                            <td><?php echo $b['id']; ?></td>
                            <td><?php echo htmlspecialchars($b['first_name'] . ' ' . $b['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($b['email']); ?></td>
                            <td><?php echo htmlspecialchars($b['phone']); ?></td>
                            <td><?php echo htmlspecialchars($b['city']); ?></td>
                            <td><?php echo $b['created_at']; ?></td>
                            <td>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Delete this beneficiary?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="beneficiary_id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn" style="background:#c33;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer class="wow">
        <p>&copy; <?php echo date('Y'); ?> UNITED COMMUNITY SUPPORT INITIATIVE</p>
    </footer>
</body>
</html>
