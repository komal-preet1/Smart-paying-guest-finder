<?php
session_start();
require "includes/database_connect.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us | SmartPGFinder</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />

    <style>
        .contact-wrapper {
            max-width: 700px;
            margin: 40px auto 60px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
            padding: 30px 25px;
        }
        .contact-wrapper h2 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        .contact-wrapper p.lead {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

  <!-- Header Section (with Home / Explore PGs / Contact / Login / Signup) -->
  <?php require "./includes/header.php"; ?>

  <nav aria-label="breadcrumb">
      <ol class="breadcrumb py-2">
          <li class="breadcrumb-item">
              <a href="index.php">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
              Contact
          </li>
      </ol>
  </nav>

  <div class="container">
      <div class="contact-wrapper">
          <h2>Contact Us</h2>
          <p class="lead">
              Have any questions about PGs, bookings, or your account?  
              Send us a message and we’ll get back to you as soon as possible.
          </p>

          <form method="POST" action="contact_handler.php">
              <div class="form-group mb-3">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
              </div>

              <div class="form-group mb-3">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
              </div>

              <div class="form-group mb-3">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" required>
              </div>

              <div class="form-group mb-4">
                  <label for="message">Message</label>
                  <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
              </div>

              <button type="submit" class="btn btn-primary">
                  <i class="fas fa-paper-plane"></i> Send Message
              </button>
          </form>
      </div>
  </div>

  <!-- Footer -->
  <?php require "./includes/footer.php"; ?>

  <!-- Modals for Signup / Login (so user can login from contact page also) -->
  <?php require "./includes/signup_modal.php"; ?>
  <?php require "./includes/login_modal.php"; ?>

  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/common.js"></script>

</body>
</html>
