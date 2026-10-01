<?php

session_start();

require_once "../config/database.php";


// =====================================
// CHECK ADMIN LOGIN
// =====================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}


// =====================================
// ONLY POST REQUEST
// =====================================

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: bookings.php");
    exit;
}


// =====================================
// GET DATA
// =====================================

$booking_id = isset($_POST["booking_id"])
    ? (int) $_POST["booking_id"]
    : 0;

$status = isset($_POST["status"])
    ? trim($_POST["status"])
    : "";


// =====================================
// VALIDATE
// =====================================

$allowed_statuses = [
    "Pending",
    "Confirmed",
    "Cancelled"
];

if (
    $booking_id <= 0 ||
    !in_array($status, $allowed_statuses, true)
) {

    header(
        "Location: bookings.php?error=failed"
    );

    exit;
}


// =====================================
// UPDATE STATUS
// =====================================

$query = "UPDATE bookings
          SET status = ?
          WHERE id = ?";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $booking_id
);

mysqli_stmt_execute($stmt);


// =====================================
// CHECK RESULT
// =====================================

if (mysqli_stmt_affected_rows($stmt) >= 0) {

    mysqli_stmt_close($stmt);

    header(
        "Location: bookings.php?success=updated"
    );

    exit;
}


mysqli_stmt_close($stmt);

header(
    "Location: bookings.php?error=failed"
);

exit;

?>