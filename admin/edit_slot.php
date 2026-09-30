<?php

session_start();

require_once "../config/database.php";

// Check admin
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}


// Get slot ID
$slot_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($slot_id <= 0) {

    header("Location: slots.php");
    exit;
}

$message = "";
$message_type = "";


// Get slot
$query = "SELECT
            id,
            slot_number,
            vehicle_type,
            location,
            status
          FROM parking_slots
          WHERE id = ?";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $slot_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) != 1) {

    mysqli_stmt_close($stmt);

    header("Location: slots.php");
    exit;
}


$slot = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// Update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $slot_number = trim($_POST["slot_number"]);
    $vehicle_type = trim($_POST["vehicle_type"]);
    $location = trim($_POST["location"]);
    $status = $_POST["status"];


    if (
        empty($slot_number) ||
        empty($vehicle_type) ||
        empty($location) ||
        empty($status)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }

    else {

        // Check duplicate slot number
        $check_query = "SELECT id
                        FROM parking_slots
                        WHERE slot_number = ?
                        AND id != ?";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $slot_number,
            $slot_id
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result(
            $check_stmt
        );


        if (mysqli_num_rows($check_result) > 0) {

            $message = "Another slot already uses this slot number.";
            $message_type = "error";

            mysqli_stmt_close($check_stmt);

        }

        else {

            mysqli_stmt_close($check_stmt);


            $update_query = "UPDATE parking_slots
                             SET
                                slot_number = ?,
                                vehicle_type = ?,
                                location = ?,
                                status = ?
                             WHERE id = ?";

            $update_stmt = mysqli_prepare(
                $conn,
                $update_query
            );

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssssi",
                $slot_number,
                $vehicle_type,
                $location,
                $status,
                $slot_id
            );


            if (mysqli_stmt_execute($update_stmt)) {

                mysqli_stmt_close($update_stmt);

                header(
                    "Location: slots.php?success=updated"
                );

                exit;

            }

            else {

                $message = "Unable to update parking slot.";
                $message_type = "error";

                mysqli_stmt_close($update_stmt);
            }
        }
    }
}

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
        Edit Parking Slot - Smart Parking
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


<header class="admin-header">

    <div class="admin-nav-container">

        <div class="admin-logo">
            🚗 Smart Parking
        </div>

        <div class="admin-nav-right">

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


<main class="admin-slots-page">

    <div class="admin-slots-container">

        <div class="admin-form-card">

            <h1>
                Edit Parking Slot
            </h1>

            <p class="admin-form-subtitle">
                Update parking slot information.
            </p>


            <?php if (!empty($message)) { ?>

                <div
                    class="admin-message <?php echo $message_type; ?>"
                >
                    <?php
                    echo htmlspecialchars($message);
                    ?>
                </div>

            <?php } ?>


            <form
                method="POST"
                action="edit_slot.php?id=<?php echo $slot_id; ?>"
            >


                <div class="admin-form-group">

                    <label for="slot_number">
                        Slot Number
                    </label>

                    <input
                        type="text"
                        id="slot_number"
                        name="slot_number"
                        value="<?php
                        echo htmlspecialchars(
                            $slot["slot_number"]
                        );
                        ?>"
                        maxlength="20"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label for="vehicle_type">
                        Vehicle Type
                    </label>

                    <select
                        id="vehicle_type"
                        name="vehicle_type"
                        required
                    >

                        <option
                            value="Car"
                            <?php
                            echo $slot["vehicle_type"]
                            == "Car"
                            ? "selected"
                            : "";
                            ?>
                        >
                            Car
                        </option>

                        <option
                            value="Bike"
                            <?php
                            echo $slot["vehicle_type"]
                            == "Bike"
                            ? "selected"
                            : "";
                            ?>
                        >
                            Bike
                        </option>

                    </select>

                </div>


                <div class="admin-form-group">

                    <label for="location">
                        Location
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?php
                        echo htmlspecialchars(
                            $slot["location"]
                        );
                        ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="Available"
                            <?php
                            echo $slot["status"]
                            == "Available"
                            ? "selected"
                            : "";
                            ?>
                        >
                            Available
                        </option>

                        <option
                            value="Occupied"
                            <?php
                            echo $slot["status"]
                            == "Occupied"
                            ? "selected"
                            : "";
                            ?>
                        >
                            Occupied
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="admin-submit-btn"
                >
                    Update Parking Slot
                </button>


            </form>


            <a
                href="slots.php"
                class="admin-back-btn"
            >
                ← Back to Parking Slots
            </a>

        </div>

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