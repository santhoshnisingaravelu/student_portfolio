<?php

session_start();

require "database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$id = (int)$_SESSION["user_id"];

$message = "";

if (isset($_POST["add"])) {

    $title =
        trim($_POST["title"]);

    $description =
        trim($_POST["description"]);

    $date =
        $_POST["achievement_date"] ?: null;

    $stmt = $conn->prepare(
        "INSERT INTO achievements
        (user_id,title,description,
         achievement_date,status)
        VALUES(?,?,?,?, 'pending')"
    );

    $stmt->bind_param(
        "isss",
        $id,
        $title,
        $description,
        $date
    );

    $stmt->execute();

    $message =
        "Achievement submitted for admin approval.";
}

$stmt = $conn->prepare(
    "SELECT *
     FROM achievements
     WHERE user_id=?
     ORDER BY achievement_date DESC,id DESC"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$rows =
$stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Achievements | SkillSphere</title>

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

<a href="portfolio.php">
Portfolio
</a>

<a href="logout.php">
Logout
</a>

</div>

</div>

</nav>

<div class="container page">

<div class="page-head">

<div>

<div class="eyebrow">
03 / Recognition
</div>

<h1>
Achievements
</h1>

<p>
Submit academic awards, certifications
and accomplishments for verification.
</p>

</div>

</div>

<div class="dashboard-grid">

<div class="form-card">

<h2>
Submit achievement
</h2>

<?php if($message): ?>

<div class="alert success">
<?=$message?>
</div>

<?php endif; ?>

<form method="post">

<label>
Title
</label>

<input
name="title"
placeholder="Best Project Award"
required
>

<label>
Description
</label>

<textarea
name="description"
required
></textarea>

<label>
Date
</label>

<input
type="date"
name="achievement_date"
>

<div style="margin-top:20px">

<button
class="btn"
name="add"
>

Submit for review

</button>

</div>

</form>

</div>

<div class="card">

<h2>
Submission history
</h2>

<div class="list">

<?php while($r=$rows->fetch_assoc()): ?>

<div class="list-item">

<h3>
<?=htmlspecialchars($r["title"])?>
</h3>

<p>
<?=htmlspecialchars($r["description"])?>
</p>

<span class="badge <?=htmlspecialchars($r["status"])?>">

<?=htmlspecialchars($r["status"])?>

</span>

</div>

<?php endwhile; ?>

<?php if($rows->num_rows===0): ?>

<p class="muted">
No achievements submitted yet.
</p>

<?php endif; ?>

</div>

</div>

</div>

</div>

</body>

</html>