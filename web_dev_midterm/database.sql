-- =============================================================
--  database.sql — Full schema + seed data for adminpanel
--  Run: mysql -u root -p < database.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS adminpanel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE adminpanel;

-- -------------------------------------------------------------
--  Table: native_users
--  Used by: auth.php (login / register), get_profile.php
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS native_users (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username   VARCHAR(50)     NOT NULL UNIQUE,
    password   VARCHAR(255)    NOT NULL,           -- bcrypt hash
    created_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
--  Table: skills
--  Used by: get_skills.php → script.js (Skills modal)
--
--  Columns expected by the JS:
--    category  — section heading (e.g. 'Frontend', 'Backend')
--    name      — badge label     (e.g. 'HTML', 'PHP')
--    color     — Bootstrap badge class (e.g. 'bg-danger')
--
--  username FK lets each user own their own skill set.
--  To enable per-user filtering, uncomment the WHERE clause
--  in get_skills.php and change execute([]) to execute([$username]).
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS skills (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username   VARCHAR(50)     NOT NULL,
    category   VARCHAR(50)     NOT NULL,
    name       VARCHAR(50)     NOT NULL,
    color      VARCHAR(50)     NOT NULL DEFAULT 'bg-secondary',
    PRIMARY KEY (id),
    CONSTRAINT fk_skills_user
        FOREIGN KEY (username) REFERENCES native_users (username)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
--  Seed: sample users
--
--  username  | plain-text password
--  ----------|--------------------
--  admin     | Admin@123
--  juan      | Juan@123
--  maria     | Maria@123
--  pedro     | Pedro@123
--  student   | Student@1
-- -------------------------------------------------------------
INSERT IGNORE INTO native_users (username, password) VALUES
    ('admin',   '$2y$12$97UOZ9aH4dsGuzVjMKrUJupA1kPbjwpjq6lSe2ow0z2rzipqSGX5.'),
    ('juan',    '$2y$12$tQ43ngkhb6Yf6TE9AlFMwu19OGRcx61T5UM6k5ZfalWcgG5PTd6.m'),
    ('maria',   '$2y$12$mx4QarW4nwclAGzTb2u/4OIlV2hrDJV7yl/dH2Dd4.JAa5VG.P2Ii'),
    ('pedro',   '$2y$12$sUj/3nxFPvS.F0.5e1QOdOGJTMOpEQw/ZXuuHVMENLhngAR7lY17m'),
    ('student', '$2y$12$KsQSSLY4y2PClyIgGCFWp.1SW4ITLiL5OWutTfnAS5kVTJiR1NwlS');

-- -------------------------------------------------------------
--  Seed: skills for the admin user
--  Matches the badges shown in the profile hero section of index.php
-- -------------------------------------------------------------
INSERT IGNORE INTO skills (username, category, name, color) VALUES
-- Frontend
('admin', 'Frontend', 'HTML',        'bg-danger'),
('admin', 'Frontend', 'CSS',         'bg-primary'),
('admin', 'Frontend', 'JavaScript',  'bg-warning text-dark'),
('admin', 'Frontend', 'Bootstrap 5', 'bg-info text-dark'),

-- Backend
('admin', 'Backend',  'PHP',         'bg-secondary'),
('admin', 'Backend',  'MySQL',       'bg-success'),

-- Libraries
('admin', 'Libraries','jQuery',      'bg-dark'),

-- Design
('admin', 'Design',   'Responsive Design', 'bg-primary');

-- -------------------------------------------------------------
--  Confirm results
-- -------------------------------------------------------------
SELECT id, username, created_at FROM native_users;
