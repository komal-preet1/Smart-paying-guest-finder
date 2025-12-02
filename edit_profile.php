<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("location:index.php");
    exit();
}
require "includes/database_connect.php";

$user_id = $_SESSION["user_id"];
$query = mysqli_query($con, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $college = $_POST['college'];

    mysqli_query($con, "UPDATE users SET full_name='$name', phone='$phone', college_name='$college' WHERE id='$user_id'");
    $_SESSION["full_name"] = $name;

    header("location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require "includes/header.php"; ?>

<div class="container mt-5">
<h3>Edit Profile</h3>
<form method="post">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?= $user['full_name'] ?>" required>
    </div>

    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?>" required>
    </div>

    <div class="form-group">
        <label>College</label>
        <input type="text" name="college" class="form-control" value="<?= $user['college_name'] ?>">
    </div>

    <button name="update" class="btn btn-primary mt-3">Update Profile</button>
</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
