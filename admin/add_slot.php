<?php

session_start();

require_once "../config/database.php";

// Check admin
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}

$message = "";
$message_type = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $slot_number = trim($_POST["slot_number"]);
    $vehicle_type = trim($_POST["vehicle_type"]);
    $location = trim($_POST["location"]);
    $status = $_POST["status"];


    // Validation

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
                        WHERE slot_number = ?";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $slot_number
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result(
            $check_stmt
        );


        if (mysqli_num_rows($check_result) > 0) {

            $message = "This slot number already exists.";
            $message_type = "error";

        }

        else {

            $insert_query = "INSERT INTO parking_slots
                            (
                                slot_number,
                                vehicle_type,
                                location,
                                status
                            )
                            VALUES (?, ?, ?, ?)";

            $insert_stmt = mysqli_prepare(
                $conn,
                $insert_query
            );

            mysqli_stmt_bind_param(
                $insert_stmt,
                "ssss",
                $slot_number,
                $vehicle_type,
                $location,
                $status
            );

            if (mysqli_stmt_execute($insert_stmt)) {

                mysqli_stmt_close($insert_stmt);
                mysqli_stmt_close($check_stmt);

                header(
                    "Location: slots.php?success=added"
                );

                exit;

            }

            else {

                $message = "Unable to add parking slot.";
                $message_type = "error";

                mysqli_stmt_close($insert_stmt);
            }
        }

        mysqli_stmt_close($check_stmt);
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
        Add Parking Slot - Smart Parking
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
                Add Parking Slot
            </h1>

            <p class="admin-form-subtitle">
                Add a new parking slot to the system.
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
                action="add_slot.php"
            >


                <div class="admin-form-group">

                    <label for="slot_number">
                        Slot Number
                    </label>

                    <input
                        type="text"
                        id="slot_number"
                        name="slot_number"
                        placeholder="Example: P09"
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

                        <option value="">
                            Select Vehicle Type
                        </option>

                        <option value="Car">
                            Car
                        </option>

                        <option value="Bike">
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
                        placeholder="Example: Ground Floor"
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

                        <option value="Available">
                            Available
                        </option>

                        <option value="Occupied">
                            Occupied
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="admin-submit-btn"
                >
                    Add Parking Slot
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

    

</footer>


</body>

</html>