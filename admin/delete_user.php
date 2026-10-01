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

if (
    $_SERVER["REQUEST_METHOD"]
    != "POST"
) {

    header("Location: users.php");
    exit;
}


// =====================================
// GET USER ID
// =====================================

$user_id = isset($_POST["user_id"])
    ? (int) $_POST["user_id"]
    : 0;

if ($user_id <= 0) {

    header(
        "Location: users.php?error=delete_failed"
    );

    exit;
}


// =====================================
// PROTECT CURRENT ADMIN
// =====================================

if (
    $user_id
    == $_SESSION["admin_id"]
) {

    header(
        "Location: users.php?error=current_admin"
    );

    exit;
}


// =====================================
// CHECK BOOKING HISTORY
// =====================================

$booking_query = "SELECT COUNT(*) AS total_bookings
                  FROM bookings
                  WHERE user_id = ?";

$booking_stmt = mysqli_prepare(
    $conn,
    $booking_query
);

mysqli_stmt_bind_param(
    $booking_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $booking_stmt
);

$booking_result =
    mysqli_stmt_get_result(
        $booking_stmt
    );

$booking_row =
    mysqli_fetch_assoc(
        $booking_result
    );

mysqli_stmt_close(
    $booking_stmt
);


// Do not delete users with booking history
if (
    $booking_row["total_bookings"] > 0
) {

    header(
        "Location: users.php?error=has_bookings"
    );

    exit;
}


// =====================================
// DELETE USER
// =====================================

$delete_query = "DELETE FROM users
                 WHERE id = ?";

$delete_stmt = mysqli_prepare(
    $conn,
    $delete_query
);

mysqli_stmt_bind_param(
    $delete_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $delete_stmt
);


if (
    mysqli_stmt_affected_rows(
        $delete_stmt
    ) > 0
) {

    mysqli_stmt_close(
        $delete_stmt
    );

    header(
        "Location: users.php?success=deleted"
    );

    exit;
}


mysqli_stmt_close(
    $delete_stmt
);

header(
    "Location: users.php?error=delete_failed"
);

exit;

?>