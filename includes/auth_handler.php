<?php
/**
 * Auth handler – admin login/logout, beneficiary registration, admin CRUD & export
 */

declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';

// ---------- Helper utilities ----------
function is_logged_in(): bool {
    return isset($_SESSION['admin_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit();
    }
}

function login_admin(string $email, string $password): bool {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $email;
        return true;
    }
    return false;
}

function logout_admin(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header('Location: index.php');
    exit();
}

// ---------- Beneficiary registration ----------
function register_beneficiary(array $data): bool {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO beneficiaries (first_name, last_name, email, phone, address, city, state, zip, created_at) VALUES (:first_name, :last_name, :email, :phone, :address, :city, :state, :zip, NOW())');
    return $stmt->execute([
        'first_name' => $data['first_name'],
        'last_name'  => $data['last_name'],
        'email'      => $data['email'],
        'phone'      => $data['phone'],
        'address'    => $data['address'],
        'city'       => $data['city'],
        'state'      => $data['state'],
        'zip'        => $data['zip'],
    ]);
}

// ---------- Admin CRUD (simplified) ----------
function get_all_beneficiaries(): array {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM beneficiaries ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function delete_beneficiary(int $id): bool {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM beneficiaries WHERE id = :id');
    return $stmt->execute(['id' => $id]);
}

function export_beneficiaries_csv(): string {
    $rows = get_all_beneficiaries();
    $output = "ID,First Name,Last Name,Email,Phone,Address,City,State,Zip,Created At\n";
    foreach ($rows as $row) {
        $output .= implode(",", array_map('escape_csv', $row)) . "\n";
    }
    return $output;
}

function escape_csv(string $value): string {
    $escaped = str_replace('"', '""', $value);
    if (preg_match('/[\r\n,]/', $escaped)) {
        $escaped = "\"{$escaped}\"";
    }
    return $escaped;
}
?>