<?php
// process.php — ANSWER KEY

// TASK 15 ANSWER: header('Content-Type: application/json')
// Must come BEFORE any echo or output
header("Content-Type: application/json");

// TASK 16 ANSWER: $_SERVER['REQUEST_METHOD']
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// TASK 17 ANSWER: trim($_POST['student_name'])
$name    = trim($_POST['student_name'] ?? '');

// TASK 18 ANSWER: trim($_POST['email'])
$email   = trim($_POST['email'] ?? '');

// TASK 19 ANSWER: trim($_POST['message'])
$message = trim($_POST['message'] ?? '');

// TASK 21 ANSWER: empty($name) — validate BEFORE sanitizing
if (empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'Name is required.']);
    exit;
}

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required.']);
    exit;
}

// TASK 22 ANSWER: FILTER_VALIDATE_EMAIL
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

// TASK 20 ANSWER: htmlspecialchars($name) — sanitize AFTER validation, before output
$name    = htmlspecialchars($name,    ENT_QUOTES, 'UTF-8');
$email   = htmlspecialchars($email,   ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// TASK 23 ANSWER: status = 'success', message includes $name
$response = [
    'status'  => 'success',
    'message' => 'Thank you, ' . $name . '! Your feedback has been received.',
    'data'    => ['name' => $name, 'email' => $email, 'message' => $message]
];

echo json_encode($response);
?>
