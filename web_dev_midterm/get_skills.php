<?php
/**
 * get_skills.php
 * AJAX endpoint — returns the logged-in user's skills as JSON.
 * Called by: script.js → $.ajax({ url: 'get_skills.php', type: 'GET' })
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

    // TODO: Replace 'your_skills_table' with your actual table name.
    //
    // The JS groups skills by 'category' and labels them with 'name'.
    // It also uses 'color' as a Bootstrap badge class — if your table
    // doesn't have a color column, remove it and set a default in JS instead.
    //
    // Expected columns:
    //   category  — skill group label shown as a section heading
    //               e.g. 'Frontend', 'Backend', 'Libraries'
    //   name      — individual skill label shown on the badge
    //               e.g. 'HTML', 'PHP', 'jQuery'
    //   color     — Bootstrap badge background class
    //               e.g. 'bg-primary', 'bg-success', 'bg-warning text-dark'
    //
    // TODO: Add a WHERE clause to filter by the logged-in user if the
    //       skills table has a user_id or username foreign key.
    $stmt = $pdo->prepare("
        SELECT category, name, color
        FROM skills
        WHERE username = ?
        ORDER BY category, name
    ");

    $stmt->execute([$username]);
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // The JS expects an array of objects with this shape:
    // [
    //   { "category": "Frontend",  "name": "HTML",       "color": "bg-danger"          },
    //   { "category": "Frontend",  "name": "CSS",        "color": "bg-primary"         },
    //   { "category": "Frontend",  "name": "JavaScript", "color": "bg-warning text-dark"},
    //   { "category": "Backend",   "name": "PHP",        "color": "bg-secondary"       },
    //   { "category": "Backend",   "name": "MySQL",      "color": "bg-success"         },
    //   { "category": "Libraries", "name": "jQuery",     "color": "bg-dark"            }
    // ]
    echo json_encode([
        'status' => 'success',
        'data'   => $skills
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
