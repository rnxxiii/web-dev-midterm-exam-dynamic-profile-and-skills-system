<?php
require_once 'auth.php';

// Retrieve flash messages from session and then clear them
$error = $_SESSION['flash_error'] ?? "";
$success = $_SESSION['flash_success'] ?? "";
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

// Determine which page to show
$action = $_GET['action'] ?? 'login';

// Apply Auth Guard — dashboard and profile require login
if ($action === 'dashboard' || $action === 'profile') {
    checkAuth();
}

// Feedback page is publicly accessible
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $action === 'profile' ? htmlspecialchars($_SESSION['user_id'] ?? 'Profile') . ' — Portfolio' : 'PHP User System' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- jQuery must be loaded BEFORE app.js so $ is available -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="app.js" defer></script>

    <style>
        /* ── Shared ─────────────────────────────────────────── */
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        /* ── Auth pages (login / register / dashboard / feedback) ── */
        body.auth-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            background: white;
            border: none;
        }
        .password-strength {
            height: 6px;
            transition: all 0.4s ease;
            margin-top: 8px;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        #responseMsg { display: none; }
        #responseMsg.success { background: #e8faf5; color: #028090; border: 1px solid #2EC4B6; }
        #responseMsg.error   { background: #fdecea; color: #C0392B; border: 1px solid #C0392B; }

        /* ── Profile page ────────────────────────────────────── */

        /* Navbar */
        #profileNav {
            background: linear-gradient(135deg, #1E2761, #408EC6);
            box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        }
        #profileNav .navbar-brand,
        #profileNav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        #profileNav .nav-link:hover {
            color: #fff !important;
        }
        #profileNav .nav-link.logout-link {
            color: #ff8a80 !important;
        }

        /* Hero section */
        .hero-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            min-height: 70vh;
            display: flex;
            align-items: center;
            padding: 5rem 0;
        }
        .hero-avatar {
            width: 130px;
            height: 130px;
            background: linear-gradient(135deg, #408EC6, #1E2761);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            border: 4px solid rgba(255,255,255,0.25);
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            margin: 0 auto 1.5rem;
        }
        .hero-section .badge {
            font-size: 0.8rem;
            padding: 0.45em 0.85em;
            border-radius: 50px;
            font-weight: 600;
            margin: 0.2rem;
        }
        .hero-section .lead {
            color: rgba(255,255,255,0.75);
            font-size: 1.15rem;
        }

        /* Cards section */
        .cards-section {
            background-color: #f0f2f5;
            padding: 5rem 0;
        }
        .profile-card {
            border: none;
            border-radius: 1.25rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            background: white;
        }
        .profile-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.13);
        }
        .profile-card .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .profile-card .card-title {
            font-weight: 700;
            font-size: 1.15rem;
        }

        /* Section heading */
        .section-heading {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.4rem;
        }
        .section-divider {
            width: 50px;
            height: 4px;
            background: linear-gradient(135deg, #1E2761, #408EC6);
            border-radius: 2px;
            margin: 0 auto 3rem;
        }

        /* Footer */
        .profile-footer {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: rgba(255,255,255,0.75);
            padding: 2rem 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="<?= $action === 'profile' ? '' : 'auth-layout' ?>">

<?php if ($action === 'profile'): ?>

    <!-- ════════════════════════════════════════════════════════
         PROFILE PAGE — Full-width layout
    ════════════════════════════════════════════════════════ -->

    <!-- TODO 2: Navbar -->
    <nav class="navbar navbar-expand-md" id="profileNav">
        <div class="container">
            <a class="navbar-brand fw-bold fs-5" href="#">
                <?= htmlspecialchars($_SESSION['user_id']) ?>
            </a>
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#profileNavMenu"
                    aria-controls="profileNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="profileNavMenu">
                <ul class="navbar-nav ms-auto gap-1">
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="auth.php?action=logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- TODO 3: Hero Section -->
    <section class="hero-section text-center" id="home">
        <div class="container">
            <div class="hero-avatar fade-in">
                <?= strtoupper(substr($_SESSION['user_id'], 0, 1)) ?>
            </div>
            <h1 class="display-4 fw-bold mb-2 fade-in">
                <?= htmlspecialchars($_SESSION['user_id']) ?>
            </h1>
            <p class="lead mb-4 fade-in">Full Stack Student Developer</p>
            <div class="mb-4 fade-in">
                <span class="badge bg-primary">HTML</span>
                <span class="badge bg-success">CSS</span>
                <span class="badge bg-warning text-dark">JavaScript</span>
                <span class="badge bg-info text-dark">Bootstrap</span>
                <span class="badge bg-danger">PHP</span>
                <span class="badge bg-secondary">MySQL</span>
            </div>
            <a href="#about" class="btn btn-light btn-lg px-5 fw-semibold fade-in"
               style="border-radius: 50px;">View My Work</a>
        </div>
    </section>

    <!-- TODO 4: Three-Column Card Section -->
    <section class="cards-section" id="about">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading">My Portfolio</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">

                <!-- About Me Card -->
                <div class="col-md-4">
                    <div class="card profile-card h-100">
                        <div class="card-body p-4">
                            <div class="card-icon">👤</div>
                            <h5 class="card-title">About Me</h5>
                            <p class="card-text text-muted">
                                I am a passionate student developer currently learning web technologies.
                                I enjoy building clean, responsive interfaces and solving real-world
                                problems through code.
                            </p>
                            <button type="button" id="btnAboutModal"
                                    class="btn btn-outline-primary mt-2 px-4"
                                    style="border-radius: 50px;">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Skills Card -->
                <div class="col-md-4" id="skills">
                    <div class="card profile-card h-100">
                        <div class="card-body p-4">
                            <div class="card-icon">⚡</div>
                            <h5 class="card-title">My Skills</h5>
                            <p class="card-text text-muted">
                                HTML &middot; CSS &middot; JavaScript &middot; Bootstrap 5 &middot;
                                PHP &middot; MySQL &middot; jQuery &middot; Responsive Design
                            </p>
                            <button type="button" id="btnSkillsModal"
                                    class="btn btn-outline-success mt-2 px-4"
                                    style="border-radius: 50px;">
                                View Skills
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact Card -->
                <div class="col-md-4" id="contact">
                    <div class="card profile-card h-100">
                        <div class="card-body p-4">
                            <div class="card-icon">✉️</div>
                            <h5 class="card-title">Contact Me</h5>
                            <p class="card-text text-muted">
                                Got a project in mind or want to collaborate? Feel free to reach out
                                through the feedback form or connect via social media.
                            </p>
                            <a href="index.php?action=feedback" class="btn btn-outline-danger mt-2 px-4"
                               style="border-radius: 50px;">Get in Touch</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ── About Me Modal ───────────────────────────────────── -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.25rem; border: none; overflow: hidden;">
                <!-- Gradient header -->
                <div class="modal-header border-0 text-white text-center d-block py-4"
                     style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold"
                         style="width:80px;height:80px;border-radius:50%;font-size:2.2rem;
                                background:linear-gradient(135deg,#408EC6,#1E2761);
                                border:3px solid rgba(255,255,255,0.25);color:white;">
                        <?= strtoupper(substr($_SESSION['user_id'], 0, 1)) ?>
                    </div>
                    <h5 class="modal-title fw-bold fs-4" id="aboutModalLabel">
                        <?= htmlspecialchars($_SESSION['user_id']) ?>
                    </h5>
                    <p class="mb-0 opacity-75">Full Stack Student Developer</p>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Body — populated via AJAX (get_profile.php) -->
                <div class="modal-body px-4 py-4" id="aboutModalBody">
                    <!-- Loading spinner shown until AJAX responds -->
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2 text-muted">Loading profile...</span>
                    </div>
                    <p class="text-muted small mb-0" style="display:none;">
                        Passionate student developer currently learning web technologies. Enjoys building
                        clean, responsive interfaces and solving real-world problems through code.
                    </p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-primary w-100 fw-semibold"
                            style="border-radius:50px;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Skills Modal ──────────────────────────────────────── -->
    <div class="modal fade" id="skillsModal" tabindex="-1" aria-labelledby="skillsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.25rem; border: none; overflow: hidden;">
                <!-- Gradient header -->
                <div class="modal-header border-0 text-white text-center d-block py-4"
                     style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:70px;height:70px;border-radius:50%;font-size:2rem;
                                background:rgba(255,255,255,0.1);border:3px solid rgba(255,255,255,0.25);">
                        ⚡
                    </div>
                    <h5 class="modal-title fw-bold fs-4" id="skillsModalLabel">My Skills</h5>
                    <p class="mb-0 opacity-75">Technologies I work with</p>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Body — populated via AJAX (get_skills.php) -->
                <div class="modal-body px-4 py-4" id="skillsModalBody">
                    <!-- Loading spinner shown until AJAX responds -->
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <span class="ms-2 text-muted">Loading skills...</span>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-success w-100 fw-semibold"
                            style="border-radius:50px;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TODO 5: Footer -->
    <footer class="profile-footer text-center">
        <p class="mb-1 fw-semibold" style="color: white;">
            <?= htmlspecialchars($_SESSION['user_id']) ?>
        </p>
        <p class="mb-0">&copy; <?= date('Y') ?> All rights reserved.</p>
    </footer>

<?php else: ?>

    <!-- ════════════════════════════════════════════════════════
         AUTH CARD LAYOUT — login / register / dashboard / feedback
    ════════════════════════════════════════════════════════ -->
    <div class="container d-flex justify-content-center">
        <div class="auth-card fade-in" id="mainContainer">

            <!-- Flash messages -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($action === 'login'): ?>
                <!-- LOGIN VIEW -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Please enter your details</p>
                </div>
                <form action="auth.php?action=login" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control py-2" placeholder="e.g. johndoe" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">Sign In</button>
                    <div class="text-center mt-4">
                        <span class="text-muted">New user?</span>
                        <a href="index.php?action=register" class="text-decoration-none fw-semibold">Create an account</a>
                    </div>
                </form>

            <?php elseif ($action === 'register'): ?>
                <!-- REGISTER VIEW -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Join our community today</p>
                </div>
                <form action="auth.php?action=register" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose Username</label>
                        <input type="text" name="username" class="form-control py-2" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" id="regPassword" class="form-control py-2" placeholder="Minimum 6 characters" required>
                        <div id="strengthMeter" class="password-strength bg-light w-100 rounded"></div>
                        <div class="d-flex justify-content-between mt-1">
                            <small id="strengthText" class="text-muted">Strength: None</small>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" id="regConfirm" name="confirm_password" class="form-control py-2" placeholder="Repeat password" required>
                    </div>
                    <button type="submit" name="register" id="btnSubmitReg" class="btn btn-success w-100 py-2 fw-bold">Register Now</button>
                    <div id="registerMsg" class="mt-3 p-3 rounded" style="display:none;"></div>
                    <div class="text-center mt-4">
                        <span class="text-muted">Already registered?</span>
                        <a href="index.php?action=login" class="text-decoration-none fw-semibold">Log in</a>
                    </div>
                </form>

            <?php elseif ($action === 'dashboard'): ?>
                <!-- DASHBOARD VIEW -->
                <div class="text-center">
                    <div class="mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px;">
                            <span class="fs-2"><?= strtoupper(substr($_SESSION['user_id'], 0, 1)) ?></span>
                        </div>
                        <h3 class="fw-bold">Welcome Home</h3>
                        <p class="text-muted">You are logged in as <strong><?= htmlspecialchars($_SESSION['user_id']) ?></strong></p>
                    </div>
                    <hr class="my-4">
                    <div class="d-grid gap-3">
                        <a href="index.php?action=profile" class="btn btn-primary py-2 fw-bold">View My Profile</a>
                        <button class="btn btn-outline-primary" id="btnInteract">Show JS Interaction</button>
                        <a href="index.php?action=feedback" class="btn btn-outline-secondary py-2 fw-bold">Student Feedback</a>
                        <a href="auth.php?action=logout" class="btn btn-danger py-2 fw-bold">Sign Out</a>
                    </div>
                </div>

            <?php elseif ($action === 'feedback'): ?>
                <!-- FEEDBACK FORM VIEW -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Student Feedback</h2>
                    <p class="text-muted">We'd love to hear from you</p>
                </div>
                <form id="feedbackForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="student_name" class="form-control py-2" placeholder="e.g. Juan dela Cruz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control py-2" placeholder="e.g. juan@school.edu" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Feedback Message</label>
                        <textarea name="message" rows="4" class="form-control" placeholder="Write your feedback here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Send Feedback</button>
                </form>
                <div id="responseMsg" class="mt-3 p-3 rounded"></div>
                <div class="text-center mt-4">
                    <a href="index.php?action=login" class="text-decoration-none fw-semibold">Back to Login</a>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endif; ?>

<!-- Bootstrap Bundle JS (required for navbar hamburger toggler) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- External Custom JavaScript -->
<script src="script.js?v=2"></script>
</body>
</html>
