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
// GET DATA
// =====================================

$user_id = isset($_POST["user_id"])
    ? (int) $_POST["user_id"]
    : 0;

$role = isset($_POST["role"])
    ? trim($_POST["role"])
    : "";


// =====================================
// VALIDATE
// =====================================

if (
    $user_id <= 0 ||
    !in_array($role, ["user", "admin"], true)
) {

    header(
        "Location: users.php?error=delete_failed"
    );

    exit;
}


// Do not change currently logged-in admin
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
// UPDATE ROLE
// =====================================

$query = "UPDATE users
          SET role = ?
          WHERE id = ?";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $role,
    $user_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


header(
    "Location: users.php?success=updated"
);

exit;

?>