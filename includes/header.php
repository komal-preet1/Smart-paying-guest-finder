<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 sticky-top">
    <div class="container">

        <!-- LEFT SIDE: LOGO -->
        <a class="navbar-brand fw-bold fs-3" href="index.php">PGLife</a>

        <!-- MOBILE TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- RIGHT SIDE: MENU -->
        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
            <ul class="navbar-nav align-items-center">

                <!-- MENU LINKS -->
                <li class="nav-item">
                    <a class="nav-link mx-2" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mx-2" href="property_list.php?city=Delhi">Explore</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mx-2" href="contact.php">Contact</a>
                </li>

                <?php if (!isset($_SESSION["user_id"])) { ?>
                    <!-- NOT LOGGED IN -->
                    <li class="nav-item">
                        <a class="nav-link text-primary mx-2"
                           href="#" data-bs-toggle="modal" data-bs-target="#login-modal">
                           Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary text-white px-3 mx-2"
                           href="#" data-bs-toggle="modal" data-bs-target="#signup-modal"
                           style="border-radius:20px;">
                           Signup
                        </a>
                    </li>

                <?php } else { 
                    $first_name = explode(" ", $_SESSION["full_name"])[0];
                ?>

                    <!-- LOGGED IN DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-primary fw-bold mx-2"
                           href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                           Hi, <?php echo htmlspecialchars($first_name); ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>

                <?php } ?>

            </ul>
        </div>

    </div>
</nav>
