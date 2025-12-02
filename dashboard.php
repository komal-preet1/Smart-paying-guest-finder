<?php
session_start();

/* User must be logged in */
if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    exit();
}

require "includes/database_connect.php";
$user_id = $_SESSION["user_id"];

/* Fetch user data */
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($con, $user_query);
$user = mysqli_fetch_assoc($user_result);

$full_name = $user["full_name"];
$email = $user["email"];
$phone = $user["phone"];
$college = $user["college_name"];

/* Fetch Interested Properties */
$sql_liked_property = "
SELECT p.*
FROM interested_users_properties iup
INNER JOIN properties p ON iup.property_id = p.id
WHERE iup.user_id = $user_id
";
$liked_property_result = mysqli_query($con, $sql_liked_property);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
<?php require "./includes/header.php"; ?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb py-2">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
  </ol>
</nav>

<div class="container my-5">
    <h2 class="text-center">My Profile</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 text-center">
            <img src="assets/user.png" class="usr-img mb-3" width="120">
        </div>
        <div class="col-md-6">
            <p><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
            <p><strong>College:</strong> <?= htmlspecialchars($college) ?></p>
        </div>
    </div>
</div>

<div class="page-container">
<h3>My Interested Properties</h3>
<hr>

<?php
if ($liked_property_result && mysqli_num_rows($liked_property_result) > 0) {
    while ($row = mysqli_fetch_assoc($liked_property_result)) {
        $pg_id = $row["id"];
        $rating = round(($row["rating_clean"] + $row["rating_food"] + $row["rating_safety"]) / 3, 1);

        /* Image */
        $img_sql = "SELECT image_path FROM property_images WHERE property_id = $pg_id LIMIT 1";
        $img_res = mysqli_query($con, $img_sql);
        $img = "assets/properties/1/1d4f0757fdb86d5f.jpg";
        if($img_res && mysqli_num_rows($img_res)>0){
            $imgRow = mysqli_fetch_assoc($img_res);
            $img = $imgRow['image_path'];
        }
?>

<div class="property-card row">
    <div class="col-md-4">
        <img src="<?= $img ?>" style="width:100%;height:180px;border-radius:8px;object-fit:cover">
    </div>

    <div class="col-md-8">
        <h5><?= htmlspecialchars($row["name"]) ?></h5>
        <p><?= htmlspecialchars($row["address"]) ?></p>

        <!-- Rating -->
        <div class="rating">
        <?php
            for($i=1;$i<=5;$i++){
                if($i <= floor($rating)){
                    echo "<i class='fas fa-star'></i>";
                }else{
                    echo "<i class='far fa-star'></i>";
                }
            }
        ?>
        </div>

        <div class="rent">
            Rs <?= $row["rent"] ?>/month
        </div>

        <a href="property_detail.php?property_id=<?= $pg_id ?>" class="btn btn-primary mt-2">View</a>
    </div>
</div>

<?php
    }
} else {
?>
<div class="text-center mt-4">
    <p>No interested properties yet.</p>
</div>
<?php } ?>


<!-- BOOKED PROPERTIES -->
<h3 class="mt-5">My Booked Properties</h3>
<hr>

<?php
$booking_sql = "
SELECT b.*, p.name AS pname, p.address, p.rent
FROM bookings b
JOIN properties p ON b.property_id = p.id
WHERE b.user_id = $user_id
";
$booking_result = mysqli_query($con,$booking_sql);

if($booking_result && mysqli_num_rows($booking_result)>0){
while($b = mysqli_fetch_assoc($booking_result)){
?>
<div class="property-card row">
    <div class="col-md-8">
        <h5><?= $b['pname'] ?></h5>
        <p><?= $b['address'] ?></p>
        <p><strong>Start:</strong> <?= $b['start_date'] ?></p>
        <p><strong>Duration:</strong> <?= $b['duration'] ?> months</p>
    </div>
    <div class="col-md-4 text-right">
        <div class="rent">Rs <?= $b['rent'] ?>/month</div>
    </div>
</div>
<?php } } else { ?>
<p class="text-center">No bookings yet.</p>
<?php } ?>

</div>

<?php require "./includes/signup_modal.php"; ?>
<?php require "./includes/login_modal.php"; ?>
<?php require "./includes/footer.php"; ?>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php mysqli_close($con); ?>
