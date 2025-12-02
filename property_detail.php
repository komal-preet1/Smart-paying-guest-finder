<?php
session_start();
require "includes/database_connect.php";

if (!isset($_GET["property_id"])) {
    header("location: index.php");
    exit();
}

if (!$con) {
    die("Database Connection Failed");
}

$pg_id = (int)$_GET["property_id"];

/* PROPERTY DATA */
$sql = "SELECT * FROM properties WHERE id=$pg_id";
$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header("location:index.php");
    exit();
}

$property = mysqli_fetch_assoc($result);

$pg_city_id = $property["city_id"];
$pg_name = $property["name"];
$pg_address = $property["address"];
$pg_description = $property["description"];
$gender_allowed = $property["gender"];
$pg_rent = $property["rent"];
$pg_rating_clean = $property["rating_clean"];
$pg_rating_food = $property["rating_food"];
$pg_rating_safety = $property["rating_safety"];
$pg_rating_overall = round(($pg_rating_clean + $pg_rating_food + $pg_rating_safety) / 3, 1);

/* CITY */
$city_query = mysqli_query($con, "SELECT name FROM cities WHERE id=$pg_city_id");
$city = mysqli_fetch_assoc($city_query);
$city_name = $city['name'];

$map_query = urlencode($pg_address . ', ' . $city_name);

/* PROPERTY IMAGES */
$img_result = mysqli_query($con, "SELECT image_path FROM property_images WHERE property_id=$pg_id");
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $pg_name; ?> | PG Life</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/common.css" rel="stylesheet">
<link href="css/property_detail.css" rel="stylesheet">
</head>

<body>
<?php require "includes/header.php"; ?>

<!-- ✅ DYNAMIC IMAGE SLIDER -->
<div id="property-images" class="carousel slide" data-ride="carousel">

<?php if ($img_result && mysqli_num_rows($img_result) > 0): ?>
<ol class="carousel-indicators">
<?php
$i = 0;
mysqli_data_seek($img_result, 0);
while ($img = mysqli_fetch_assoc($img_result)) { ?>
<li data-target="#property-images" data-slide-to="<?php echo $i; ?>" class="<?php echo ($i==0?'active':''); ?>"></li>
<?php $i++; } ?>
</ol>

<div class="carousel-inner">
<?php
$i = 0;
mysqli_data_seek($img_result, 0);
while ($img = mysqli_fetch_assoc($img_result)) { ?>
<div class="carousel-item <?php echo ($i==0?'active':''); ?>">
<img src="<?php echo $img['image_path']; ?>" class="d-block w-100" style="max-height:420px; width:100%; height:auto; object-fit:contain; background:#000;">
</div>
<?php $i++; } ?>
</div>

<a class="carousel-control-prev" href="#property-images" data-slide="prev">
<span class="carousel-control-prev-icon"></span>
</a>
<a class="carousel-control-next" href="#property-images" data-slide="next">
<span class="carousel-control-next-icon"></span>
</a>

<?php else: ?>
<img src="assets/bg2.png" class="d-block w-100" style="max-height:420px; width:100%; height:auto; object-fit:contain; background:#000;">
<?php endif; ?>

</div>

<!-- ✅ PROPERTY INFO -->
<div class="container mt-4">
<h2><?php echo $pg_name; ?></h2>
<p><?php echo $pg_address; ?></p>
<p><strong>City:</strong> <?php echo $city_name; ?></p>
<p><strong>Gender:</strong> <?php echo ucfirst($gender_allowed); ?></p>
<p><strong>Rent:</strong> ₹<?php echo $pg_rent; ?>/month</p>
<p><strong>Rating:</strong> <?php echo $pg_rating_overall; ?> ⭐</p>

<a href="booking.php?property_id=<?php echo $pg_id; ?>" class="btn btn-primary mb-3">Book Now</a>
<hr>

<h4>Location on Map</h4>
<div class="mb-4" style="border-radius:12px; overflow:hidden; box-shadow:0 0 12px rgba(0,0,0,0.15);">
    <iframe width="100%" height="350" style="border:0;" loading="lazy" allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q=<?php echo $map_query; ?>&output=embed"></iframe>
</div>

<h4>About Property</h4>
<p><?php echo $pg_description; ?></p>

<hr>

<h4>Amenities</h4>
<div class="row">
<?php
$amenities_query = "
SELECT amenities.name, amenities.icon 
FROM properties_amenities 
JOIN amenities ON properties_amenities.amenity_id = amenities.id 
WHERE properties_amenities.property_id = $pg_id";

$amenities_result = mysqli_query($con,$amenities_query);

while($am = mysqli_fetch_assoc($amenities_result)){
?>
<div class="col-md-3">
<img src="assets/amenities/<?php echo $am['icon']; ?>.svg" width="24">
<?php echo $am['name']; ?>
</div>
<?php } ?>
</div>

</div>

<?php require "includes/footer.php"; ?>
<?php include "includes/chatbot_widget.php"; ?>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php mysqli_close($con); ?>