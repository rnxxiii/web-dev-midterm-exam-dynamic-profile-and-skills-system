<?php
/**
 * auth.php - Core Authentication Logic with MySQL/PDO
 */
session_start();

// Check if db.php exists before requiring it to prevent fatal errors
if (!file_exists('db.php')) {
    $_SESSION['flash_error'] = "Configuration error: db.php missing.";
    header("Location: index.php?action=login");
    exit();
}

require_once 'db.php'; // Include database connection

$action = $_GET['action'] ?? 'login';

// 1. Handle Logout
if ($action === 'logout') {
    session_destroy();
    header("Location: index.php?action=login");
    exit();
}

// 2. Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // REGISTRATION LOGIC
    if ($action === 'register') {
        $isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($isAjax) {
            header('Content-Type: application/json');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        // Server-side validation
        if (empty($username)) {
            echo json_encode(['status' => 'error', 'message' => 'Username is required.']);
            exit();
        }

        if (strlen($password) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
            exit();
        }

        if ($password !== $confirm) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
            exit();
        }

        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM native_users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
                exit();
            }

            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO native_users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);

            echo json_encode([
                'status'   => 'success',
                'message'  => 'Registration successful! Redirecting to login...',
                'redirect' => 'index.php?action=login'
            ]);
            exit();

        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit();
        }
    } 
    
    // LOGIN LOGIC
    if (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM native_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['username'];
                header("Location: index.php?action=profile");
                exit();
            } else {
                $_SESSION['flash_error'] = "Invalid username or password.";
                header("Location: index.php?action=login");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
            header("Location: index.php?action=login");
        }
        exit();
    }
}

// 3. Auth Guard Helper
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit();
    }
}
?>