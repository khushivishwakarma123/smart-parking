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
// MESSAGES
// =====================================

$message = "";
$message_type = "";


// User role updated
if (
    isset($_GET["success"]) &&
    $_GET["success"] == "updated"
) {

    $message = "User role updated successfully.";
    $message_type = "success";

}


// User deleted
elseif (
    isset($_GET["success"]) &&
    $_GET["success"] == "deleted"
) {

    $message = "User deleted successfully.";
    $message_type = "success";

}


// Delete failed
elseif (
    isset($_GET["error"]) &&
    $_GET["error"] == "delete_failed"
) {

    $message = "Unable to delete the user.";
    $message_type = "error";

}


// User has bookings
elseif (
    isset($_GET["error"]) &&
    $_GET["error"] == "has_bookings"
) {

    $message = "This user cannot be deleted because booking records exist.";
    $message_type = "error";

}


// Cannot delete current admin
elseif (
    isset($_GET["error"]) &&
    $_GET["error"] == "current_admin"
) {

    $message = "You cannot delete the currently logged-in admin.";
    $message_type = "error";

}


// =====================================
// GET ALL USERS
// =====================================

$query = "SELECT
            id,
            name,
            email,
            phone,
            role,
            created_at
          FROM users
          ORDER BY id ASC";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Manage Users - Smart Parking
    </title>

    <link
        rel="stylesheet"
        href="/smart-parking/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/smart-parking/css/admin_users.css"
    >

</head>

<body>


<!-- =====================================
     ADMIN HEADER
===================================== -->

<header class="admin-header">

    <div class="admin-nav-container">

        <div class="admin-logo">
            🚗 Smart Parking
        </div>

        <div class="admin-nav-right">

            <span class="admin-name">

                🛡️

                <?php
                echo htmlspecialchars(
                    $_SESSION["admin_name"]
                );
                ?>

            </span>

            <a
                href="dashboard.php"
                class="admin-site-link"
            >
                Dashboard
            </a>

            <a
                href="logout.php"
                class="admin-logout"
            >
                Logout
            </a>

        </div>

    </div>

</header>


<!-- =====================================
     MAIN
===================================== -->

<main class="admin-users-page">

    <div class="admin-users-container">


        <!-- Page Heading -->

        <div class="users-page-header">

            <div>

                <p class="users-label">
                    ADMIN PANEL
                </p>

                <h1>
                    Manage Users
                </h1>

                <p>
                    View and manage registered users.
                </p>

            </div>

        </div>


        <!-- Message -->

        <?php if (!empty($message)) { ?>

            <div
                class="admin-user-message
                <?php echo $message_type; ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>


        <!-- Users Table -->

        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ) { ?>


            <div class="admin-users-table-container">

                <table class="admin-users-table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Registered
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while (
                            $user =
                            mysqli_fetch_assoc($result)
                        ) { ?>


                            <tr>


                                <!-- ID -->

                                <td>

                                    #

                                    <?php
                                    echo (int)
                                        $user["id"];
                                    ?>

                                </td>


                                <!-- NAME -->

                                <td>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $user["name"]
                                        );
                                        ?>

                                    </strong>

                                    <?php
                                    if (
                                        $user["id"]
                                        == $_SESSION["admin_id"]
                                    ) {
                                    ?>

                                        <br>

                                        <span
                                            class="current-user"
                                        >
                                            Current Admin
                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $user["email"]
                                    );
                                    ?>

                                </td>


                                <!-- PHONE -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $user["phone"]
                                    );
                                    ?>

                                </td>


                                <!-- ROLE -->

                                <td>

                                    <?php
                                    if (
                                        $user["role"]
                                        == "admin"
                                    ) {
                                    ?>

                                        <span
                                            class="user-role admin-role"
                                        >
                                            Admin
                                        </span>

                                    <?php
                                    } else {
                                    ?>

                                        <span
                                            class="user-role normal-role"
                                        >
                                            User
                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- CREATED -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $user["created_at"]
                                    );
                                    ?>

                                </td>


                                <!-- ACTION -->

                                <td>


                                    <?php
                                    if (
                                        $user["id"]
                                        != $_SESSION["admin_id"]
                                    ) {
                                    ?>


                                        <!-- Change Role -->

                                        <form
                                            method="POST"
                                            action="update_user_role.php"
                                            class="user-action-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php
                                                echo (int)
                                                    $user["id"];
                                                ?>"
                                            >


                                            <select
                                                name="role"
                                                class="role-select"
                                                required
                                            >

                                                <option
                                                    value="user"
                                                    <?php
                                                    echo $user["role"]
                                                    == "user"
                                                    ? "selected"
                                                    : "";
                                                    ?>
                                                >
                                                    User
                                                </option>

                                                <option
                                                    value="admin"
                                                    <?php
                                                    echo $user["role"]
                                                    == "admin"
                                                    ? "selected"
                                                    : "";
                                                    ?>
                                                >
                                                    Admin
                                                </option>

                                            </select>


                                            <button
                                                type="submit"
                                                class="role-update-btn"
                                            >
                                                Update
                                            </button>

                                        </form>


                                        <!-- Delete -->

                                        <form
                                            method="POST"
                                            action="delete_user.php"
                                            class="delete-user-form"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php
                                                echo (int)
                                                    $user["id"];
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="delete-user-btn"
                                            >
                                                Delete
                                            </button>

                                        </form>


                                    <?php } else { ?>


                                        <span
                                            class="protected-admin"
                                        >
                                            Protected
                                        </span>


                                    <?php } ?>


                                </td>


                            </tr>


                        <?php } ?>


                    </tbody>

                </table>

            </div>


        <?php } else { ?>


            <div class="no-users">

                <div class="no-users-icon">
                    👥
                </div>

                <h2>
                    No Users Found
                </h2>

                <p>
                    No registered users are available.
                </p>

            </div>


        <?php } ?>


    </div>

</main>


<footer class="admin-footer">

    <p>

        &copy; 2026 Smart Parking
        Slot Booking & Management System

    </p>

</footer>


</body>

</html>