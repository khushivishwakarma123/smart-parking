<?php

session_start();

require_once "config/database.php";

// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Get booking ID from POST
$booking_id = isset($_POST["booking_id"])
    ? (int) $_POST["booking_id"]
    : 0;

if ($booking_id <= 0) {

    header("Location: my_bookings.php");
    exit;
}


// Cancel only the booking belonging to the logged-in user
$query = "UPDATE bookings
          SET status = 'Cancelled'
          WHERE id = ?
          AND user_id = ?
          AND status != 'Cancelled'";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $booking_id,
    $user_id
);

mysqli_stmt_execute($stmt);


// Check whether a booking was actually updated
if (mysqli_stmt_affected_rows($stmt) > 0) {

    mysqli_stmt_close($stmt);

    header(
        "Location: my_bookings.php?cancelled=1"
    );

    exit;

} else {

    mysqli_stmt_close($stmt);

    header(
        "Location: my_bookings.php?cancel_error=1"
    );

    exit;
}

?>