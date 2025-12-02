<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("location:index.php");
    exit();
}
require "includes/database_connect.php";

$user_id = $_SESSION["user_id"];
$message = "";

if (isset($_POST['change'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $check = mysqli_query($con, "SELECT password FROM users WHERE id='$user_id'");
    $row = mysqli_fetch_assoc($check);

    if ($row['password'] !== $old) {
        $message = "Old password incorrect!";
    } else {
        mysqli_query($con, "UPDATE users SET password='$new' WHERE id='$user_id'");
        $message = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require "includes/header.php"; ?>

<div class="container mt-5">
<h3>Change Password</h3>

<?php if($message) echo "<p class='text-danger'>$message</p>"; ?>

<form method="post">
    <div class="form-group">
        <label>Old Password</label>
        <input type="password" name="old_password" class="form-control" required>
    </div>

    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>
    </div>

    <button name="change" class="btn btn-warning mt-3">Change Password</button>
</form>
</div>
</body>
</html>
