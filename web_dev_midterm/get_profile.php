<?php
/**
 * get_profile.php
 * AJAX endpoint — returns the logged-in user's profile details as JSON.
 * Called by: script.js → $.ajax({ url: 'get_profile.php', type: 'GET' })
 */
session_start();
header('Content-Type: application/json');

// Auth check — reject unauthenticated requests
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit();
}

require_once 'db.php';

try {
    $username = $_SESSION['user_id'];

    // TODO: Replace the SELECT columns with the actual fields in your profile table.
    //       Common fields: full_name, course, year_level, email, bio, profile_photo
    //
    // TODO: Replace 'native_users' below if profile data lives in a different table
    //       (e.g. a separate 'student_profiles' table joined to native_users).
    $stmt = $pdo->prepare("
        SELECT
            username,
            full_name,
            course,
            year_level,
            email,
            created_at
        FROM native_users           
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo json_encode(['status' => 'error', 'message' => 'Profile not found.']);
        exit();
    }

    // The JS in script.js reads these keys from response.data:
    //   username     — always present (from session)
    //   full_name    — TODO: add when available
    //   course       — TODO: add when available
    //   year_level   — TODO: add when available
    //   email        — TODO: add when available
    //   bio          — TODO: add when available
    //   created_at   — member since date
    echo json_encode([
        'status' => 'success',
        'data'   => $profile
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
