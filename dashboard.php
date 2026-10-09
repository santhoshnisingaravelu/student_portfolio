<?php

session_start();

require "database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT COUNT(*) c
     FROM skills
     WHERE user_id=?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$skills =
(int)$stmt->get_result()
->fetch_assoc()["c"];


$stmt = $conn->prepare(
    "SELECT COUNT(*) c
     FROM projects
     WHERE user_id=?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$projects =
(int)$stmt->get_result()
->fetch_assoc()["c"];


$stmt = $conn->prepare(
    "SELECT COUNT(*) c
     FROM achievements
     WHERE user_id=?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$achievements =
(int)$stmt->get_result()
->fetch_assoc()["c"];


$stmt = $conn->prepare(
    "SELECT COUNT(*) c
     FROM achievements
     WHERE user_id=?
     AND status='approved'"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$approved =
(int)$stmt->get_result()
->fetch_assoc()["c"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Dashboard | SkillSphere</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

<div class="container nav-inner">

<a class="logo" href="index.php">
Skill<span>Sphere</span>
</a>

<div class="nav-links">

<a href="dashboard.php">
Dashboard
</a>

<a href="profile.php">
Profile
</a>

<a href="skills.php">
Skills
</a>

<a href="achievements.php">
Achievements
</a>

<a href="projects.php">
Projects
</a>

<a href="portfolio.php">
Portfolio
</a>

<a class="btn" href="logout.php">
Logout
</a>

</div>

</div>

</nav>

<main class="page">

<div class="container">

<div class="page-head">

<div>

<div class="eyebrow">
Student workspace
</div>

<h1>
Welcome,
<?=htmlspecialchars($_SESSION["name"])?>
</h1>

<p>
Manage your academic identity
from one dashboard.
</p>

</div>

</div>

<div class="stats">

<div class="stat">

<div class="label">
Skills
</div>

<div class="num">
<?=$skills?>
</div>

<a class="muted" href="skills.php">
Manage →
</a>

</div>

<div class="stat">

<div class="label">
Projects
</div>

<div class="num">
<?=$projects?>
</div>

<a class="muted" href="projects.php">
Manage →
</a>

</div>

<div class="stat">

<div class="label">
Achievements
</div>

<div class="num">
<?=$achievements?>
</div>

<span class="muted">
<?=$approved?> approved
</span>

</div>

<div class="stat">

<div class="label">
Portfolio
</div>

<div class="num">
LIVE
</div>

<a class="muted" href="portfolio.php">
View →
</a>

</div>

</div>

<div class="dashboard-grid">

<div class="card">

<div class="eyebrow">
Quick actions
</div>

<h2>
Build your portfolio
</h2>

<div class="actions">

<a class="btn" href="profile.php">
Complete profile
</a>

<a class="btn secondary" href="skills.php">
Add skill
</a>

<a class="btn secondary" href="projects.php">
Add project
</a>

<a class="btn secondary" href="achievements.php">
Add achievement
</a>

</div>

</div>

<div class="card">

<div class="eyebrow">
Demo flow
</div>

<h2>
How it works
</h2>

<p class="muted">

Register → build profile → add
skills/projects → submit achievements
→ admin approves → showcase portfolio.

</p>

</div>

</div>

</div>

</main>

</body>

</html>