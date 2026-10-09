<?php

session_start();

require "database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT
        u.name,
        u.email,
        p.*
     FROM users u
     LEFT JOIN profiles p
     ON u.id=p.user_id
     WHERE u.id=?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$profile =
$stmt->get_result()
->fetch_assoc();


$stmt = $conn->prepare(
    "SELECT *
     FROM skills
     WHERE user_id=?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$skills =
$stmt->get_result();


$stmt = $conn->prepare(
    "SELECT *
     FROM projects
     WHERE user_id=?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$projects =
$stmt->get_result();


$stmt = $conn->prepare(
    "SELECT *
     FROM achievements
     WHERE user_id=?
     AND status='approved'
     ORDER BY achievement_date DESC,id DESC"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$achievements =
$stmt->get_result();


$initials = "";

foreach (
    explode(
        " ",
        trim($profile["name"] ?? "Student")
    )
    as $part
) {

    $initials .=
        strtoupper(
            substr($part,0,1)
        );
}

$initials =
    substr($initials,0,2);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>
<?=htmlspecialchars($profile["name"])?>
| Portfolio
</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

<div class="container nav-inner">

<a class="logo" href="dashboard.php">
Skill<span>Sphere</span>
</a>

<div class="nav-links">

<a href="dashboard.php">
Dashboard
</a>

<a href="profile.php">
Edit profile
</a>

<a href="logout.php">
Logout
</a>

</div>

</div>

</nav>

<main>

<section class="portfolio-hero">

<div class="container">

<div class="profile-banner">

<div class="big-avatar">
<?=$initials?>
</div>

<div>

<div class="eyebrow">
Student portfolio
</div>

<h1>
<?=htmlspecialchars($profile["name"])?>
</h1>

<p class="muted">

<?=htmlspecialchars(
$profile["department"] ?? "Student"
)?>

·

<?=htmlspecialchars(
$profile["year"] ?? ""
)?>

</p>

</div>

</div>

<p
style="
max-width:760px;
line-height:1.8;
color:var(--muted);
margin-top:25px;
"
>

<?=nl2br(
htmlspecialchars(
$profile["bio"] ??
"Complete your profile to introduce yourself."
)
)?>

</p>

</div>

</section>

<section class="section">

<div class="container">

<div class="section-title">

<div>

<div class="eyebrow">
Capabilities
</div>

<h2>
Skills
</h2>

</div>

</div>

<div class="grid-3">

<?php while($r=$skills->fetch_assoc()): ?>

<div class="card">

<h3>
<?=htmlspecialchars($r["skill_name"])?>
</h3>

<div class="skill">

<span class="muted">
<?=htmlspecialchars($r["skill_level"])?>
</span>

<span>
✦
</span>

</div>

</div>

<?php endwhile; ?>

<?php if($skills->num_rows===0): ?>

<div class="card">

<p>
Add skills from your dashboard.
</p>

</div>

<?php endif; ?>

</div>

</div>

</section>

<section class="section">

<div class="container">

<div class="section-title">

<div>

<div class="eyebrow">
Selected work
</div>

<h2>
Projects
</h2>

</div>

</div>

<div class="grid-3">

<?php while($r=$projects->fetch_assoc()): ?>

<div class="card">

<h3>
<?=htmlspecialchars($r["title"])?>
</h3>

<p>
<?=nl2br(
htmlspecialchars(
$r["description"]
)
)?>
</p>

<p>

<strong>
Technologies
</strong>

<br>

<span class="muted">
<?=htmlspecialchars(
$r["technologies"]
)?>
</span>

</p>

</div>

<?php endwhile; ?>

<?php if($projects->num_rows===0): ?>

<div class="card">

<p>
Add projects from your dashboard.
</p>

</div>

<?php endif; ?>

</div>

</div>

</section>

<section class="section">

<div class="container">

<div class="section-title">

<div>

<div class="eyebrow">
Verified recognition
</div>

<h2>
Achievements
</h2>

</div>

</div>

<div class="grid-3">

<?php while($r=$achievements->fetch_assoc()): ?>

<div class="card">

<span class="badge approved">
Approved
</span>

<h3 style="margin-top:14px">

<?=htmlspecialchars(
$r["title"]
)?>

</h3>

<p>

<?=htmlspecialchars(
$r["description"]
)?>

</p>

<p class="muted">

<?=htmlspecialchars(
$r["achievement_date"] ?? ""
)?>

</p>

</div>

<?php endwhile; ?>

<?php if($achievements->num_rows===0): ?>

<div class="card">

<p>
No approved achievements yet.
</p>

</div>

<?php endif; ?>

</div>

</div>

</section>

</main>

<footer>

<div class="container">

Built with HTML, CSS, PHP, MySQL & Perl
• SkillSphere

</div>

</footer>

</body>

</html>