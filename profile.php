<?php

session_start();

require "database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$id = (int)$_SESSION["user_id"];

$message = "";

$stmt = $conn->prepare(
    "SELECT *
     FROM profiles
     WHERE user_id=?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$profile =
$stmt->get_result()
->fetch_assoc() ?: [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $register_no =
        trim($_POST["register_no"]);

    $department =
        trim($_POST["department"]);

    $year =
        trim($_POST["year"]);

    $phone =
        trim($_POST["phone"]);

    $bio =
        trim($_POST["bio"]);

    $stmt = $conn->prepare(
        "SELECT id
         FROM profiles
         WHERE user_id=?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $existing =
        $stmt->get_result()
        ->fetch_assoc();

    if ($existing) {

        $stmt = $conn->prepare(
            "UPDATE profiles
             SET register_no=?,
                 department=?,
                 year=?,
                 phone=?,
                 bio=?
             WHERE user_id=?"
        );

        $stmt->bind_param(
            "sssssi",
            $register_no,
            $department,
            $year,
            $phone,
            $bio,
            $id
        );

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO profiles
             (user_id,register_no,department,
              year,phone,bio)
             VALUES(?,?,?,?,?,?)"
        );

        $stmt->bind_param(
            "isssss",
            $id,
            $register_no,
            $department,
            $year,
            $phone,
            $bio
        );
    }

    $stmt->execute();

    $message =
        "Profile saved successfully.";

    $profile = [
        "register_no" => $register_no,
        "department" => $department,
        "year" => $year,
        "phone" => $phone,
        "bio" => $bio
    ];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Profile | SkillSphere</title>

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

<div class="container form-wrap">

<div class="form-card">

<div class="eyebrow">
01 / Identity
</div>

<h1>
Student profile
</h1>

<p class="muted">
Add the academic information that appears
on your portfolio.
</p>

<?php if($message): ?>

<div class="alert success">
<?=htmlspecialchars($message)?>
</div>

<?php endif; ?>

<form method="post">

<label>
Register number
</label>

<input
name="register_no"
value="<?=htmlspecialchars($profile["register_no"]??"")?>"
>

<label>
Department
</label>

<input
name="department"
value="<?=htmlspecialchars($profile["department"]??"")?>"
placeholder="BCA"
>

<label>
Year
</label>

<input
name="year"
value="<?=htmlspecialchars($profile["year"]??"")?>"
placeholder="III Year"
>

<label>
Phone
</label>

<input
name="phone"
value="<?=htmlspecialchars($profile["phone"]??"")?>"
>

<label>
Bio
</label>

<textarea
name="bio"
placeholder="Write a short professional introduction..."
><?=htmlspecialchars($profile["bio"]??"")?></textarea>

<div style="margin-top:22px">

<button
class="btn"
type="submit"
>

Save profile

</button>

</div>

</form>

</div>

</div>

</body>

</html>