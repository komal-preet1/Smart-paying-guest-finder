<?php
session_start();
require "includes/database_connect.php";

if (!$con) {
    die("Database connection failed");
}

// Get property_id from GET or POST
$property_id = 0;
if (isset($_GET['property_id'])) {
    $property_id = (int)$_GET['property_id'];
} elseif (isset($_POST['property_id'])) {
    $property_id = (int)$_POST['property_id'];
}

// If no property id, go back to home
if ($property_id <= 0) {
    header("Location: index.php");
    exit();
}

// If user not logged in, remember target and send to home with login flag
if (!isset($_SESSION['user_id'])) {
    $_SESSION['after_login_redirect'] = "booking.php?property_id=" . $property_id;
    header("Location: index.php?login=1");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch property info
$prop_sql = "SELECT p.*, c.name AS city_name FROM properties p LEFT JOIN cities c ON p.city_id=c.id WHERE p.id=$property_id";
$prop_res = mysqli_query($con, $prop_sql);
$property = $prop_res ? mysqli_fetch_assoc($prop_res) : null;

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? '';
    $duration   = (int)($_POST['duration'] ?? 0);
    $message    = $_POST['message'] ?? '';

    if ($start_date && $duration > 0) {
        $stmt = mysqli_prepare($con, "INSERT INTO bookings (user_id, property_id, start_date, duration, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iisis", $user_id, $property_id, $start_date, $duration, $message);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Your booking request has been submitted!";
            } else {
                $error_message = "Could not save booking. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
    } else {
        $error_message = "Please fill all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book PG | SmartPGFinder</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <style>
        body { background:#f5f5f5; }
        .booking-wrapper{
            max-width:600px;
            margin:40px auto;
            background:#fff;
            border-radius:10px;
            padding:25px 30px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        .booking-wrapper h3{
            margin-bottom:10px;
        }
        label{
            font-weight:600;
            margin-top:10px;
        }
        input, textarea{
            width:100%;
            margin-top:5px;
            margin-bottom:10px;
            padding:8px 10px;
            border-radius:5px;
            border:1px solid #ccc;
            font-size:14px;
        }
        button{
            margin-top:10px;
        }
        .msg-success{color:green; font-weight:600;}
        .msg-error{color:red; font-weight:600;}
    </style>
</head>
<body>
<?php require "includes/header.php"; ?>

<div class="page-container">
    <div class="booking-wrapper">
        <h3>Book PG</h3>
        <?php if ($property): ?>
            <p><strong><?php echo htmlspecialchars($property['name']); ?></strong></p>
            <p><?php echo htmlspecialchars($property['address']); ?></p>
            <p>City: <?php echo htmlspecialchars($property['city_name']); ?> | Rent: ₹<?php echo htmlspecialchars($property['rent']); ?>/month</p>
            <hr>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="msg-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="msg-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post" action="booking.php">
            <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>

            <label for="duration">Duration (Months):</label>
            <input type="number" id="duration" name="duration" min="1" placeholder="Duration in months" required>

            <label for="message">Message (Optional):</label>
            <textarea id="message" name="message" placeholder="Your message to the owner"></textarea>

            <button type="submit" class="btn btn-primary">Submit Booking</button>
        </form>
    </div>
</div>

<?php require "includes/footer.php"; ?>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php mysqli_close($con); ?>
