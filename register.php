<?php

session_start();

require "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name === "" || $email === "" || $password === "") {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email=?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $error = "An account with this email already exists.";

        } else {

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name,email,password,role)
                VALUES (?,?,?,'student')"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hash
            );

            if ($stmt->execute()) {

                header(
                    "Location: login.php?registered=1"
                );

                exit;
            }

            $error = "Registration failed.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Create Account | SkillSphere</title>

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

<a href="login.php">Login</a>

</div>

</div>

</nav>

<div class="container form-wrap">

<div class="form-card">

<div class="eyebrow">
Start your academic identity
</div>

<h1>
Create your account
</h1>

<p class="muted">
Build a professional student portfolio in a few steps.
</p>

<?php if($error): ?>

<div class="alert error">

<?=htmlspecialchars($error)?>

</div>

<?php endif; ?>

<form method="post">

<label>
Full name
</label>

<input
name="name"
required
>

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
minlength="6"
required
>

<div style="margin-top:22px">

<button
class="btn"
type="submit"
>

Create account →

</button>

</div>

</form>

<p class="muted">

Already registered?

<a
href="login.php"
style="color:var(--primary)"
>
Login here
</a>

</p>

</div>

</div>

</body>

</html>