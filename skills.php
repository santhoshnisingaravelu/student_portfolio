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

    $name =
        trim($_POST["skill_name"]);

    $level =
        trim($_POST["skill_level"]);

    if ($name !== "") {

        $stmt = $conn->prepare(
            "INSERT INTO skills
             (user_id,skill_name,skill_level)
             VALUES(?,?,?)"
        );

        $stmt->bind_param(
            "iss",
            $id,
            $name,
            $level
        );

        $stmt->execute();

        $message = "Skill added.";
    }
}

if (isset($_GET["delete"])) {

    $sid = (int)$_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM skills
         WHERE id=? AND user_id=?"
    );

    $stmt->bind_param(
        "ii",
        $sid,
        $id
    );

    $stmt->execute();

    header("Location: skills.php");

    exit;
}

$stmt = $conn->prepare(
    "SELECT *
     FROM skills
     WHERE user_id=?
     ORDER BY id DESC"
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

<title>Skills | SkillSphere</title>

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
02 / Capabilities
</div>

<h1>
My skills
</h1>

<p>
Add the skills you want recruiters
or tutors to notice.
</p>

</div>

</div>

<div class="dashboard-grid">

<div class="form-card">

<h2>
Add a skill
</h2>

<?php if($message): ?>

<div class="alert success">
<?=$message?>
</div>

<?php endif; ?>

<form method="post">

<label>
Skill name
</label>

<input
name="skill_name"
placeholder="PHP / Java / Communication"
required
>

<label>
Level
</label>

<select name="skill_level">

<option>
Beginner
</option>

<option>
Intermediate
</option>

<option selected>
Advanced
</option>

</select>

<div style="margin-top:20px">

<button
class="btn"
name="add"
>

Add skill

</button>

</div>

</form>

</div>

<div class="card">

<h2>
Current skills
</h2>

<div class="list">

<?php while($r=$rows->fetch_assoc()): ?>

<div class="list-item">

<h3>
<?=htmlspecialchars($r["skill_name"])?>
</h3>

<p>
<?=htmlspecialchars($r["skill_level"])?>
</p>

<a
class="small-btn reject"
href="?delete=<?=$r["id"]?>"
>
Remove
</a>

</div>

<?php endwhile; ?>

<?php if($rows->num_rows===0): ?>

<p class="muted">
No skills added yet.
</p>

<?php endif; ?>

</div>

</div>

</div>

</div>

</body>

</html>