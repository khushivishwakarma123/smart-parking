<?php

session_start();

require_once "../config/database.php";

// Check admin
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}


// Only allow POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: slots.php");
    exit;
}


// Get slot ID
$slot_id = isset($_POST["slot_id"])
    ? (int) $_POST["slot_id"]
    : 0;

if ($slot_id <= 0) {

    header("Location: slots.php?error=delete_failed");
    exit;
}


// Check whether this slot has bookings
$booking_query = "SELECT COUNT(*) AS total_bookings
                  FROM bookings
                  WHERE slot_id = ?";

$booking_stmt = mysqli_prepare(
    $conn,
    $booking_query
);

mysqli_stmt_bind_param(
    $booking_stmt,
    "i",
    $slot_id
);

mysqli_stmt_execute($booking_stmt);

$booking_result = mysqli_stmt_get_result(
    $booking_stmt
);

$booking_row = mysqli_fetch_assoc(
    $booking_result
);

mysqli_stmt_close($booking_stmt);


// Do not delete a slot that has booking history
if ($booking_row["total_bookings"] > 0) {

    header(
        "Location: slots.php?error=has_bookings"
    );

    exit;
}


// Delete slot
$delete_query = "DELETE FROM parking_slots
                 WHERE id = ?";

$delete_stmt = mysqli_prepare(
    $conn,
    $delete_query
);

mysqli_stmt_bind_param(
    $delete_stmt,
    "i",
    $slot_id
);

mysqli_stmt_execute($delete_stmt);


if (mysqli_stmt_affected_rows($delete_stmt) > 0) {

    mysqli_stmt_close($delete_stmt);

    header(
        "Location: slots.php?success=deleted"
    );

    exit;

}


mysqli_stmt_close($delete_stmt);

header(
    "Location: slots.php?error=delete_failed"
);

exit;

?>