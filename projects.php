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

    $tech =
        trim($_POST["technologies"]);

    $stmt = $conn->prepare(
        "INSERT INTO projects
        (user_id,title,description,technologies)
        VALUES(?,?,?,?)"
    );

    $stmt->bind_param(
        "isss",
        $id,
        $title,
        $description,
        $tech
    );

    $stmt->execute();

    $message =
        "Project added.";
}

if (isset($_GET["delete"])) {

    $pid =
        (int)$_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM projects
         WHERE id=? AND user_id=?"
    );

    $stmt->bind_param(
        "ii",
        $pid,
        $id
    );

    $stmt->execute();

    header("Location: projects.php");

    exit;
}

$stmt = $conn->prepare(
    "SELECT *
     FROM projects
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

<title>Projects | SkillSphere</title>

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
04 / Work
</div>

<h1>
Projects
</h1>

<p>
Turn your academic work into
a clear project showcase.
</p>

</div>

</div>

<div class="dashboard-grid">

<div class="form-card">

<h2>
Add project
</h2>

<?php if($message): ?>

<div class="alert success">
<?=$message?>
</div>

<?php endif; ?>

<form method="post">

<label>
Project title
</label>

<input
name="title"
required
placeholder="Student Portfolio System"
>

<label>
Description
</label>

<textarea
name="description"
required
></textarea>

<label>
Technologies
</label>

<input
name="technologies"
placeholder="HTML, CSS, PHP, MySQL"
>

<div style="margin-top:20px">

<button
class="btn"
name="add"
>

Add project

</button>

</div>

</form>

</div>

<div class="card">

<h2>
My projects
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

<p>

<strong>
Tech:
</strong>

<?=htmlspecialchars($r["technologies"])?>

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
No projects added yet.
</p>

<?php endif; ?>

</div>

</div>

</div>

</div>

</body>

</html>