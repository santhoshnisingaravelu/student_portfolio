<?php
session_start();

require "database.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

/* =========================
   APPROVE / REJECT ACHIEVEMENT
========================= */
if (
    isset($_GET["action"], $_GET["id"]) &&
    in_array($_GET["action"], ["approved", "rejected"], true)
) {
    $id = (int) $_GET["id"];
    $status = $_GET["action"];

    $stmt = $conn->prepare(
        "UPDATE achievements
         SET status=?
         WHERE id=?"
    );

    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit;
}

/* =========================
   CONSOLIDATED STATISTICS
========================= */

$totalStudents = 0;
$totalProfiles = 0;
$totalSkills = 0;
$totalProjects = 0;
$totalAchievements = 0;
$pendingAchievements = 0;
$approvedAchievements = 0;

/* Total students */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role='student'"
);

if ($q) {
    $totalStudents = (int) $q->fetch_assoc()["total"];
}

/* Profiles completed */
$q = $conn->query(
    "SELECT COUNT(DISTINCT p.user_id) AS total
     FROM profiles p
     JOIN users u ON u.id = p.user_id
     WHERE u.role='student'"
);

if ($q) {
    $totalProfiles = (int) $q->fetch_assoc()["total"];
}

/* Total skills */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM skills"
);

if ($q) {
    $totalSkills = (int) $q->fetch_assoc()["total"];
}

/* Total projects */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM projects"
);

if ($q) {
    $totalProjects = (int) $q->fetch_assoc()["total"];
}

/* Total achievements */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM achievements"
);

if ($q) {
    $totalAchievements = (int) $q->fetch_assoc()["total"];
}

/* Pending achievements */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM achievements
     WHERE status='pending'"
);

if ($q) {
    $pendingAchievements = (int) $q->fetch_assoc()["total"];
}

/* Approved achievements */
$q = $conn->query(
    "SELECT COUNT(*) AS total
     FROM achievements
     WHERE status='approved'"
);

if ($q) {
    $approvedAchievements = (int) $q->fetch_assoc()["total"];
}

/* =========================
   STUDENT CONSOLIDATED REPORT
========================= */

$studentReport = $conn->query(
    "SELECT
        u.id,
        u.name,
        u.email,
        COALESCE(p.register_no, '-') AS register_no,
        COALESCE(p.department, '-') AS department,
        COALESCE(p.year, '-') AS year,
        COUNT(DISTINCT s.id) AS skill_count,
        COUNT(DISTINCT pr.id) AS project_count,
        COUNT(DISTINCT a.id) AS achievement_count
     FROM users u

     LEFT JOIN profiles p
        ON p.user_id = u.id

     LEFT JOIN skills s
        ON s.user_id = u.id

     LEFT JOIN projects pr
        ON pr.user_id = u.id

     LEFT JOIN achievements a
        ON a.user_id = u.id

     WHERE u.role='student'

     GROUP BY
        u.id,
        u.name,
        u.email,
        p.register_no,
        p.department,
        p.year

     ORDER BY u.id DESC"
);

/* =========================
   ACHIEVEMENT REVIEW
========================= */

$result = $conn->query(
    "SELECT
        a.*,
        u.name,
        u.email
     FROM achievements a

     JOIN users u
        ON a.user_id = u.id

     ORDER BY a.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        Admin | SkillSphere
    </title>

    <link rel="stylesheet"
          href="style.css">

    <style>

        .admin-section-title {
            margin: 34px 0 14px;
        }

        .admin-section-title h2 {
            margin: 0 0 6px;
        }

        .admin-section-title p {
            margin: 0;
            opacity: .75;
        }

        .stats-grid {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            padding: 22px;
            border-radius: 18px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
        }

        .stat-label {
            font-size: 13px;
            opacity: .7;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-note {
            margin-top: 9px;
            font-size: 12px;
            opacity: .65;
        }

        .summary-row {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .consolidated-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .consolidated-table th,
        .consolidated-table td {
            padding: 13px 12px;
            text-align: left;
            border-bottom:
                1px solid rgba(255,255,255,.08);
            vertical-align: middle;
        }

        .consolidated-table th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .7;
            white-space: nowrap;
        }

        .consolidated-table td {
            font-size: 14px;
        }

        .count-pill {
            display: inline-flex;
            min-width: 30px;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 999px;
            background: rgba(255,255,255,.07);
            font-weight: 700;
        }

        @media (max-width: 900px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .summary-row {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 560px) {

            .stats-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="container nav-inner">

        <a class="logo"
           href="index.php">

            Skill<span>Sphere</span>

        </a>

        <div class="nav-links">

            <a href="admin.php">
                Admin
            </a>

            <a href="logout.php">
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container page">

    <!-- =========================
         HEADER
    ========================== -->

    <div class="page-head">

        <div>

            <div class="eyebrow">
                Administrator console
            </div>

            <h1>
                Consolidated Student Dashboard
            </h1>

            <p>
                View overall student activity and manage
                achievements from one screen.
            </p>

        </div>

    </div>


    <!-- =========================
         OVERALL STATISTICS
    ========================== -->

    <div class="admin-section-title">

        <h2>
            Overall Statistics
        </h2>

        <p>
            Live totals from the student portfolio database.
        </p>

    </div>


    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-label">
                Total Students
            </div>

            <div class="stat-value">
                <?= $totalStudents ?>
            </div>

            <div class="stat-note">
                Registered students
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Profiles Completed
            </div>

            <div class="stat-value">
                <?= $totalProfiles ?>
            </div>

            <div class="stat-note">
                Students with profiles
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Total Skills
            </div>

            <div class="stat-value">
                <?= $totalSkills ?>
            </div>

            <div class="stat-note">
                Skills entered
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Total Projects
            </div>

            <div class="stat-value">
                <?= $totalProjects ?>
            </div>

            <div class="stat-note">
                Projects recorded
            </div>

        </div>

    </div>


    <!-- =========================
         ACHIEVEMENT SUMMARY
    ========================== -->

    <div class="summary-row">


        <div class="stat-card">

            <div class="stat-label">
                Total Achievements
            </div>

            <div class="stat-value">
                <?= $totalAchievements ?>
            </div>

            <div class="stat-note">
                Submitted achievements
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Pending Achievements
            </div>

            <div class="stat-value">
                <?= $pendingAchievements ?>
            </div>

            <div class="stat-note">
                Awaiting review
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Approved Achievements
            </div>

            <div class="stat-value">
                <?= $approvedAchievements ?>
            </div>

            <div class="stat-note">
                Verified achievements
            </div>

        </div>

    </div>


    <!-- =========================
         STUDENT CONSOLIDATED REPORT
    ========================== -->

    <div class="admin-section-title">

        <h2>
            Student Consolidated Report
        </h2>

        <p>
            Complete student summary in one view.
        </p>

    </div>


    <div class="card">

        <div class="table-wrap">

            <table class="consolidated-table">

                <thead>

                    <tr>

                        <th>
                            Student
                        </th>

                        <th>
                            Register No.
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Year
                        </th>

                        <th>
                            Skills
                        </th>

                        <th>
                            Projects
                        </th>

                        <th>
                            Achievements
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php

                if (
                    $studentReport &&
                    $studentReport->num_rows > 0
                ):

                ?>

                    <?php

                    while (
                        $student =
                        $studentReport->fetch_assoc()
                    ):

                    ?>

                        <tr>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $student["name"]
                                    ) ?>
                                </strong>

                                <div style="
                                    font-size:12px;
                                    opacity:.65;
                                    margin-top:4px;
                                ">

                                    <?= htmlspecialchars(
                                        $student["email"]
                                    ) ?>

                                </div>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $student["register_no"]
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $student["department"]
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $student["year"]
                                ) ?>
                            </td>


                            <td>

                                <span class="count-pill">

                                    <?= (int)
                                        $student["skill_count"]
                                    ?>

                                </span>

                            </td>


                            <td>

                                <span class="count-pill">

                                    <?= (int)
                                        $student["project_count"]
                                    ?>

                                </span>

                            </td>


                            <td>

                                <span class="count-pill">

                                    <?= (int)
                                        $student["achievement_count"]
                                    ?>

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>


                <?php else: ?>

                    <tr>

                        <td
                            colspan="7"
                            class="muted"
                        >

                            No student records available yet.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================
         ACHIEVEMENT REVIEW
    ========================== -->

    <div class="admin-section-title">

        <h2>
            Achievement Review
        </h2>

        <p>
            Approve or reject student achievements.
        </p>

    </div>


    <div class="card">

        <div class="list">


            <?php

            while (
                $r =
                $result->fetch_assoc()
            ):

            ?>

                <div class="list-item">


                    <div style="
                        display:flex;
                        justify-content:space-between;
                        gap:15px;
                        flex-wrap:wrap;
                    ">


                        <div>

                            <h3>

                                <?= htmlspecialchars(
                                    $r["title"]
                                ) ?>

                            </h3>


                            <p>

                                <strong>

                                    <?= htmlspecialchars(
                                        $r["name"]
                                    ) ?>

                                </strong>

                                ·

                                <?= htmlspecialchars(
                                    $r["email"]
                                ) ?>

                            </p>


                            <p>

                                <?= htmlspecialchars(
                                    $r["description"]
                                ) ?>

                            </p>

                        </div>


                        <div>

                            <span
                                class="
                                badge
                                <?= htmlspecialchars(
                                    $r["status"]
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $r["status"]
                                ) ?>

                            </span>

                        </div>


                    </div>


                    <div class="actions-row">


                        <?php

                        if (
                            $r["status"] !== "approved"
                        ):

                        ?>

                            <a
                                class="small-btn approve"
                                href="?action=approved&id=<?= (int) $r["id"] ?>"
                            >
                                Approve
                            </a>

                        <?php endif; ?>


                        <?php

                        if (
                            $r["status"] !== "rejected"
                        ):

                        ?>

                            <a
                                class="small-btn reject"
                                href="?action=rejected&id=<?= (int) $r["id"] ?>"
                            >
                                Reject
                            </a>

                        <?php endif; ?>


                    </div>

                </div>

            <?php endwhile; ?>


            <?php

            if (
                $result->num_rows === 0
            ):

            ?>

                <p class="muted">
                    No achievement submissions yet.
                </p>

            <?php endif; ?>


        </div>

    </div>

</div>

</body>

</html>