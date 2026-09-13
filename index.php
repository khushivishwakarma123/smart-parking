<?php

require_once "config/database.php";

$query = "SELECT COUNT(*) AS total FROM parking_slots";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

$total_slots = $row["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Smart Parking</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <h1>Smart Parking Slot Booking & Management System</h1>

    <p>Database connected successfully!</p>

    <p>Total Parking Slots: <?php echo $total_slots; ?></p>

</body>

</html>