<?php
session_start();
require "includes/database_connect.php";

// Hero Section Setup
if ($con)
{
    php_mysqli_connect('includes/database_connect.php')
}
   else {
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        subtitle VARCHAR(255),
        image_path VARCHAR(255)
    )");

    $r = mysqli_query($con, "SELECT * FROM hero_section LIMIT 1");

    if ($r && mysqli_num_rows($r) > 0) {
        $hero = mysqli_fetch_assoc($r);
    } else {
        $hero = [
            "title" => "Find Your Perfect PG Easily",
            "subtitle" => "Affordable • Safe • Verified PGs",
            "image_path" => "assets/bg3.png"
        ];
        mysqli_query($con, "INSERT INTO hero_section (title, subtitle, image_path)
        VALUES ('{$hero["title"]}', '{$hero["subtitle"]}', '{$hero["image_path"]}')");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGLife - Find Best PGs</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .hero-section{
            height:80vh;
            position:relative;
        }
        .hero-overlay{
            position:absolute; inset:0; background:rgba(0,0,0,0.55);
        }
        .hover-card:hover{transform:translateY(-5px); transition:0.3s;}
    </style>
</head>

<body>

<!-- NAVBAR -->
<?php require "includes/header.php"; ?>

<!-- HERO SECTION -->
<section class="hero-section d-flex align-items-center"
         style="background:url('<?php echo $hero['image_path']; ?>') center/cover no-repeat;">
    <div class="hero-overlay"></div>

    <div class="container text-center text-white position-relative">
        <h1 class="display-3 fw-bold mb-3">
            <?php echo htmlspecialchars($hero["title"]); ?>
        </h1>

        <p class="lead mb-4">
            <?php echo htmlspecialchars($hero["subtitle"]); ?>
        </p>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="property_list.php" method="GET" class="input-group shadow-lg">
                    <select name="city" class="form-select form-select-lg" required>
                        <option value="" disabled selected>Select City</option>
                        <?php
                        $cities = mysqli_query($con,"SELECT * FROM cities ORDER BY name ASC");
                        while($c = mysqli_fetch_assoc($cities)){
                            echo "<option value='{$c['name']}'>{$c['name']}</option>";
                        }
                        ?>
                    </select>
                    <button class="btn btn-primary btn-lg px-4">Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PGs -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-4">Featured PGs</h2>
        <div class="row g-4">

            <?php
            $pg_res = mysqli_query($con, "
                SELECT p.*, c.name AS city_name
                FROM properties p
                JOIN cities c ON p.city_id = c.id
                ORDER BY p.id DESC
                LIMIT 6
            ");

            while($pg = mysqli_fetch_assoc($pg_res)){
                $img_q = mysqli_query($con,
                    "SELECT image_path FROM property_images WHERE property_id={$pg['id']} LIMIT 1");
                $img = mysqli_fetch_assoc($img_q);
                $imgPath = $img ? $img['image_path'] : 'assets/bg2.png';
            ?>

            <div class="col-md-4">
                <div class="card shadow-sm rounded-4 overflow-hidden h-100 hover-card">
                    <img src="<?= $imgPath ?>" class="card-img-top"
                         style="height:220px;object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= $pg['name'] ?></h5>
                        <p class="text-muted"><?= $pg['city_name'] ?> • <?= ucfirst($pg['gender']) ?> PG</p>
                        <p class="fw-bold">₹<?= $pg['rent'] ?> / month</p>
                        <a href="property_detail.php?property_id=<?= $pg['id'] ?>"
                           class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>

            <?php } ?>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-5 bg-dark text-white">
    <div class="container text-center">
        <p>&copy; 2025 PGLife — All rights reserved</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- LOGIN & SIGNUP MODALS -->
<?php require "./includes/login_modal.php"; ?>
<?php require "./includes/signup_modal.php"; ?>

</body>
</html>
