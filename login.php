<?php

session_start();

require "database.php";

$error = "";

$success =
isset($_GET["registered"])
? "Account created successfully. Please login."
: "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id,name,password,role
         FROM users
         WHERE email=?"
    );

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if (
        $user &&
        password_verify(
            $password,
            $user["password"]
        )
    ) {

        $_SESSION["user_id"] =
            $user["id"];

        $_SESSION["name"] =
            $user["name"];

        $_SESSION["role"] =
            $user["role"];

        if ($user["role"] === "admin") {

            header("Location: admin.php");

        } else {

            header("Location: dashboard.php");

        }

        exit;

    }

    $error =
        "Invalid email or password.";
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Login | SkillSphere</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

<div class="container nav-inner">

<a class="logo" href="index.php">
Skill<span>Sphere</span>
</a>

<div class="nav-links">

<a href="index.php">
Home
</a>

<a href="register.php">
Register
</a>

</div>

</div>

</nav>

<div class="container form-wrap">

<div class="form-card">

<div class="eyebrow">
Welcome back
</div>

<h1>
Sign in
</h1>

<p class="muted">
Access your portfolio workspace.
</p>

<?php if($success): ?>

<div class="alert success">

<?=htmlspecialchars($success)?>

</div>

<?php endif; ?>

<?php if($error): ?>

<div class="alert error">

<?=htmlspecialchars($error)?>

</div>

<?php endif; ?>

<form method="post">

<label>
Email
</label>

<input
type="email"
name="email"
required
>

<label>
Password
</label>

<input
type="password"
name="password"
required
>

<div style="margin-top:22px">

<button
class="btn"
type="submit"
>

Login →

</button>

</div>

</form>

<div
class="card"
style="margin-top:22px;padding:16px"
>

<strong>
Demo Admin
</strong>

<p
class="muted"
style="margin-bottom:0"
>

admin@gmail.com / password

</p>

</div>

</div>

</div>

</body>

</html>