<?php
  // Silent database connector for AJAX (no warnings printed)
  // Uses same environment detection as main connector.

  $host = $_SERVER['HTTP_HOST'] ?? '';

  if ($host === 'localhost' || $host === '127.0.0.1') {
      $db_hostname = "127.0.0.1";
      $db_username = "root";
      $db_password = "";
      $db_name     = "pg_life";
  } else {
      // 🔴 LIVE SERVER SETTINGS (InfinityFree / 000webhost etc.)
      // 👉 IMPORTANT: Change these values to match your hosting DB.
      $db_hostname = "YOUR_LIVE_DB_HOST";
      $db_username = "YOUR_LIVE_DB_USERNAME";
      $db_password = "YOUR_LIVE_DB_PASSWORD";
      $db_name     = "YOUR_LIVE_DB_NAME";
  }

  // This function will not echo/show any error on the webpage even if the function fails or there is some error...
  // Using it so the result of this page will only be the JSON encoded string to be parsed by the AJAX requests...
  $con = @mysqli_connect( $db_hostname , $db_username , $db_password , $db_name );
?>
