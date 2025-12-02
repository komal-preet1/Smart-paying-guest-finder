<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Reuse common DB connection (works on localhost + live)
    require "includes/database_connect.php";

    // Ensure contact_us table exists
    $createTableSql = "CREATE TABLE IF NOT EXISTS contact_us (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(200) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        message VARCHAR(1000) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    mysqli_query($con, $createTableSql);

    // Get and escape values
    $name         = mysqli_real_escape_string($con, $_POST['name'] ?? '');
    $email        = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $phone_number = mysqli_real_escape_string($con, $_POST['phone_number'] ?? '');
    $message      = mysqli_real_escape_string($con, $_POST['message'] ?? '');

    if ($name === '' || $email === '' || $phone_number === '' || $message === '') {
        echo "<script>alert('All fields are required.'); window.location.href='contact.php';</script>";
        exit();
    }

    $stmt = mysqli_prepare($con, "INSERT INTO contact_us (name, email, phone_number, message) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone_number, $message);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Your message has been sent successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Failed to send your message. Please try again.'); window.location.href='contact.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}
?>
