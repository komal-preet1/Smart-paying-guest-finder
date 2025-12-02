
<?php
$host = "sql210.infinityfree.com";
$user = "if0_40552927";
$pass = "Kom123al"; 
$dbname = "if0_40552927_pgfinder";

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
