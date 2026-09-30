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

if (isset($_GET["success"])) {

    if ($_GET["success"] == "added") {
        $message = "Parking slot added successfully.";
        $message_type = "success";
    }

    elseif ($_GET["success"] == "updated") {
        $message = "Parking slot updated successfully.";
        $message_type = "success";
    }

    elseif ($_GET["success"] == "deleted") {
        $message = "Parking slot deleted successfully.";
        $message_type = "success";
    }

}

elseif (isset($_GET["error"])) {

    if ($_GET["error"] == "delete_failed") {
        $message = "Unable to delete the parking slot.";
        $message_type = "error";
    }

    elseif ($_GET["error"] == "has_bookings") {
        $message = "This slot cannot be deleted because it has booking records.";
        $message_type = "error";
    }

}


// =====================================
// GET ALL PARKING SLOTS
// =====================================

$query = "SELECT
            id,
            slot_number,
            vehicle_type,
            location,
            status
          FROM parking_slots
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
        Manage Parking Slots - Smart Parking
    </title>

    <link
        rel="stylesheet"
        href="/smart-parking/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/smart-parking/css/admin_slots.css"
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

<main class="admin-slots-page">

    <div class="admin-slots-container">


        <!-- Page Header -->

        <div class="slots-page-header">

            <div>

                <h1>
                    Manage Parking Slots
                </h1>

                <p>
                    Add, edit and manage your parking slots.
                </p>

            </div>

            <a
                href="add_slot.php"
                class="add-slot-btn"
            >
                + Add New Slot
            </a>

        </div>


        <!-- Messages -->

        <?php if (!empty($message)) { ?>

            <div
                class="admin-message <?php echo $message_type; ?>"
            >
                <?php
                echo htmlspecialchars($message);
                ?>
            </div>

        <?php } ?>


        <!-- Slot Table -->

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <div class="admin-slots-table-container">

                <table class="admin-slots-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Slot Number</th>
                            <th>Vehicle Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($slot = mysqli_fetch_assoc($result)) { ?>

                            <tr>

                                <td>
                                    <?php
                                    echo (int) $slot["id"];
                                    ?>
                                </td>

                                <td class="slot-number">
                                    <?php
                                    echo htmlspecialchars(
                                        $slot["slot_number"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $slot["vehicle_type"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $slot["location"]
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php
                                    if (
                                        $slot["status"]
                                        == "Available"
                                    ) {
                                    ?>

                                        <span
                                            class="admin-slot-status available"
                                        >
                                            Available
                                        </span>

                                    <?php
                                    } else {
                                    ?>

                                        <span
                                            class="admin-slot-status occupied"
                                        >
                                            Occupied
                                        </span>

                                    <?php
                                    }

                                    ?>

                                </td>

                                <td>

                                    <a
                                        href="edit_slot.php?id=<?php echo (int) $slot["id"]; ?>"
                                        class="slot-edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        method="POST"
                                        action="delete_slot.php"
                                        style="display: inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this parking slot?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="slot_id"
                                            value="<?php
                                            echo (int) $slot["id"];
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="slot-delete-btn"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <div class="no-slots-message">

                <h2>
                    No Parking Slots Found
                </h2>

                <p>
                    Add your first parking slot.
                </p>

                <br>

                <a
                    href="add_slot.php"
                    class="add-slot-btn"
                >
                    + Add Slot
                </a>

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