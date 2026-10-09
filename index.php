<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SkillSphere | Student Portfolio</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

<div class="container nav-inner">

<a class="logo" href="index.php">
Skill<span>Sphere</span>
</a>

<div class="nav-links">

<a href="index.php">Home</a>

<?php if(isset($_SESSION['user_id'])): ?>

<a href="dashboard.php">Dashboard</a>
<a href="portfolio.php">Portfolio</a>

<?php if($_SESSION['role'] === 'admin'): ?>
<a href="admin.php">Admin</a>
<?php endif; ?>

<a class="btn" href="logout.php">Logout</a>

<?php else: ?>

<a href="login.php">Login</a>
<a class="btn" href="register.php">Get Started</a>

<?php endif; ?>

</div>

</div>

</nav>

<main>

<section class="hero">

<div class="container hero-grid">

<div>

<div class="eyebrow">
Academic Identity • Skills • Achievements
</div>

<h1>
Your work.<br>
<span>Your story.</span><br>
Your portfolio.
</h1>

<p>
SkillSphere is a web-based student portfolio and academic
achievement management system that brings profiles, skills,
projects and verified achievements together in one professional space.
</p>

<div class="actions">

<a class="btn" href="register.php">
Create my portfolio →
</a>

<a class="btn secondary" href="#features">
Explore features
</a>

</div>

</div>

<div class="hero-card">

<div class="mini-profile">

<div class="avatar">
SS
</div>

<div>

<strong>Student Profile</strong>

<br>

<span class="muted">
BCA • Academic Portfolio
</span>

</div>

</div>

<div class="muted">
Portfolio completeness
</div>

<div class="progress" style="margin:10px 0 22px">

<span style="width:82%"></span>

</div>

<div class="grid-3">

<div>
<strong>12</strong>
<br>
<span class="muted">Skills</span>
</div>

<div>
<strong>06</strong>
<br>
<span class="muted">Projects</span>
</div>

<div>
<strong>09</strong>
<br>
<span class="muted">Awards</span>
</div>

</div>

</div>

</div>

</section>

<section class="section" id="features">

<div class="container">

<div class="section-title">

<div>

<div class="eyebrow">
Everything in one place
</div>

<h2>
Built for student growth
</h2>

</div>

<p>
Simple enough for a semester project,
polished enough for a real portfolio demo.
</p>

</div>

<div class="grid-4">

<div class="card">

<div class="icon">◈</div>

<h3>
Profile
</h3>

<p>
Maintain registration, department, year,
contact and bio details.
</p>

</div>

<div class="card">

<div class="icon">✦</div>

<h3>
Skills
</h3>

<p>
Show technical and professional skills
with a visible skill level.
</p>

</div>

<div class="card">

<div class="icon">◆</div>

<h3>
Achievements
</h3>

<p>
Submit achievements for administrator
review and approval.
</p>

</div>

<div class="card">

<div class="icon">▣</div>

<h3>
Projects
</h3>

<p>
Present projects, descriptions and
technologies used.
</p>

</div>

</div>

</div>

</section>

</main>

<footer>

<div class="container">

SkillSphere — Student Skills, Portfolio &
Academic Achievement Management System

</div>

</footer>

</body>
</html>