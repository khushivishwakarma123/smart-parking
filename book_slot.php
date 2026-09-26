<?php

session_start();

require_once "config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


// Get slot ID
$slot_id = isset($_GET["slot_id"])
    ? (int) $_GET["slot_id"]
    : 0;

if ($slot_id <= 0) {
    header("Location: parking_slots.php");
    exit;
}


$message = "";
$message_type = "";

$success = isset($_GET["success"]) &&
           $_GET["success"] == "1";

$success_booking_id = isset($_GET["booking_id"])
    ? (int) $_GET["booking_id"]
    : 0;


// Get slot information
$query = "SELECT id, slot_number, vehicle_type, location, status
          FROM parking_slots
          WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $slot_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) != 1) {

    mysqli_stmt_close($stmt);

    header("Location: parking_slots.php");
    exit;
}

$slot = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// Check current slot status
if ($slot["status"] != "Available") {

    header("Location: parking_slots.php");
    exit;
}


// Process booking form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vehicle_number = trim($_POST["vehicle_number"]);
    $booking_date = $_POST["booking_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];


    // ================================
    // BASIC VALIDATION
    // ================================

    if (
        empty($vehicle_number) ||
        empty($booking_date) ||
        empty($start_time) ||
        empty($end_time)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }


    // Check date
    elseif ($booking_date < date("Y-m-d")) {

        $message = "Booking date cannot be in the past.";
        $message_type = "error";

    }


    // Check time
    elseif ($start_time >= $end_time) {

        $message = "End time must be later than start time.";
        $message_type = "error";

    }


    else {

        // ==========================================
        // CHECK FOR OVERLAPPING BOOKING
        // ==========================================

        $check_query = "SELECT id
                        FROM bookings
                        WHERE slot_id = ?
                        AND booking_date = ?
                        AND status != 'Cancelled'
                        AND start_time < ?
                        AND end_time > ?
                        LIMIT 1";

        $check_stmt = mysqli_prepare(
            $conn,
            $check_query
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "isss",
            $slot_id,
            $booking_date,
            $end_time,
            $start_time
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result(
            $check_stmt
        );


        // Booking already exists
        if (mysqli_num_rows($check_result) > 0) {

            $message = "This parking slot is already booked for the selected time.";

            $message_type = "error";

            mysqli_stmt_close($check_stmt);

        }


        // No overlapping booking
        else {

            mysqli_stmt_close($check_stmt);


            // ==========================================
            // INSERT BOOKING
            // ==========================================

            $insert_query = "INSERT INTO bookings
                            (
                                user_id,
                                slot_id,
                                vehicle_number,
                                booking_date,
                                start_time,
                                end_time,
                                status
                            )
                            VALUES
                            (?, ?, ?, ?, ?, ?, 'Pending')";

            $insert_stmt = mysqli_prepare(
                $conn,
                $insert_query
            );

            mysqli_stmt_bind_param(
                $insert_stmt,
                "iissss",
                $_SESSION["user_id"],
                $slot_id,
                $vehicle_number,
                $booking_date,
                $start_time,
                $end_time
            );


            if (mysqli_stmt_execute($insert_stmt)) {

                $booking_id = mysqli_insert_id($conn);

                mysqli_stmt_close($insert_stmt);


                // Redirect back to booking page
                header(
                    "Location: book_slot.php?slot_id=" .
                    $slot_id .
                    "&success=1&booking_id=" .
                    $booking_id
                );

                exit;

            } else {

                $message = "Booking failed. Please try again.";

                $message_type = "error";

                mysqli_stmt_close($insert_stmt);
            }
        }
    }
}


include "includes/header.php";

?>

<section class="booking-section">

    <div class="container">

        <div class="booking-heading">

            <p class="parking-slots-label">
                SMART PARKING
            </p>

            <h1>
                Book Parking Slot
            </h1>

            <p>
                Enter your vehicle and booking details.
            </p>

        </div>


        <div class="booking-container">


            <!-- Success Message -->

            <?php if ($success) { ?>

                <div class="message success">

                    Booking successful!

                    <?php if ($success_booking_id > 0) { ?>

                        <br>

                        Booking ID:
                        <?php echo $success_booking_id; ?>

                    <?php } ?>

                </div>

            <?php } ?>


            <!-- Error Message -->

            <?php if (!empty($message)) { ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php } ?>


            <!-- Selected Slot -->

            <div class="selected-slot">

                <div class="slot-icon">
                    🅿️
                </div>

                <h2>

                    <?php
                    echo htmlspecialchars(
                        $slot["slot_number"]
                    );
                    ?>

                </h2>

                <p>

                    <strong>Vehicle Type:</strong>

                    <?php
                    echo htmlspecialchars(
                        $slot["vehicle_type"]
                    );
                    ?>

                </p>

                <p>

                    <strong>Location:</strong>

                    <?php
                    echo htmlspecialchars(
                        $slot["location"]
                    );
                    ?>

                </p>

                <span class="status available">
                    Available
                </span>

            </div>


            <!-- Booking Form -->

            <form
                method="POST"
                action="book_slot.php?slot_id=<?php echo $slot_id; ?>"
            >


                <!-- Vehicle Number -->

                <div class="form-group">

                    <label for="vehicle_number">
                        Vehicle Number
                    </label>

                    <input
                        type="text"
                        id="vehicle_number"
                        name="vehicle_number"
                        placeholder="Example: MH12AB1234"
                        maxlength="20"
                        required
                    >

                </div>


                <!-- Booking Date -->

                <div class="form-group">

                    <label for="booking_date">
                        Booking Date
                    </label>

                    <input
                        type="date"
                        id="booking_date"
                        name="booking_date"
                        min="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>


                <!-- Time -->

                <div class="booking-time-grid">

                    <div class="form-group">

                        <label for="start_time">
                            Start Time
                        </label>

                        <input
                            type="time"
                            id="start_time"
                            name="start_time"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="end_time">
                            End Time
                        </label>

                        <input
                            type="time"
                            id="end_time"
                            name="end_time"
                            required
                        >

                    </div>

                </div>


                <!-- Confirm Button -->

                <button
                    type="submit"
                    class="btn btn-primary booking-btn"
                >
                    Confirm Booking
                </button>

            </form>

        </div>


        <div class="back-dashboard">

            <a
                href="parking_slots.php"
                class="btn"
            >
                ← Back to Parking Slots
            </a>

        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>