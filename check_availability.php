<?php

session_start();

require_once "config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$available_slots = [];
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $booking_date = $_POST["booking_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];

    // Basic validation
    if (empty($booking_date) || empty($start_time) || empty($end_time)) {

        $message = "Please select date, start time and end time.";
        $message_type = "error";

    } elseif ($start_time >= $end_time) {

        $message = "End time must be later than start time.";
        $message_type = "error";

    } else {

        // Find slots that are already booked during this time
        $query = "SELECT DISTINCT slot_id
                  FROM bookings
                  WHERE booking_date = ?
                  AND status != 'Cancelled'
                  AND start_time < ?
                  AND end_time > ?";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $booking_date,
            $end_time,
            $start_time
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $booked_slot_ids = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $booked_slot_ids[] = $row["slot_id"];
        }

        mysqli_stmt_close($stmt);


        // Get all available slots
        $slot_query = "SELECT id, slot_number, vehicle_type, location
                       FROM parking_slots
                       WHERE status = 'Available'
                       ORDER BY id ASC";

        $slot_result = mysqli_query($conn, $slot_query);

        while ($slot = mysqli_fetch_assoc($slot_result)) {

            if (!in_array($slot["id"], $booked_slot_ids)) {

                $available_slots[] = $slot;
            }
        }

        if (count($available_slots) > 0) {

            $message = count($available_slots) .
                       " parking slot(s) available.";
            $message_type = "success";

        } else {

            $message = "No parking slots available for the selected time.";
            $message_type = "error";
        }
    }
}

include "includes/header.php";

?>

<section class="availability-check-section">

    <div class="container">

        <div class="availability-heading">

            <p class="parking-slots-label">
                SMART PARKING
            </p>

            <h1>Check Slot Availability</h1>

            <p>
                Select your date and time to find available parking slots.
            </p>

        </div>


        <div class="availability-form-container">

            <form method="POST" action="check_availability.php">

                <div class="availability-form-grid">

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


                <button
                    type="submit"
                    class="btn btn-primary availability-btn"
                >
                    Check Availability
                </button>

            </form>

        </div>


        <?php if (!empty($message)) { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <?php if (count($available_slots) > 0) { ?>

            <div class="available-results">

                <h2>
                    Available Parking Slots
                </h2>

                <div class="slots-grid">

                    <?php foreach ($available_slots as $slot) { ?>

                        <div class="slot-card">

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

                            <p class="slot-detail">
                                <strong>Vehicle:</strong>
                                <?php
                                echo htmlspecialchars(
                                    $slot["vehicle_type"]
                                );
                                ?>
                            </p>

                            <p class="slot-detail">
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

                    <?php } ?>

                </div>

            </div>

        <?php } ?>

        <div class="back-dashboard">

            <a href="dashboard.php" class="btn">
                ← Back to Dashboard
            </a>

        </div>

    </div>

</section>

<?php

include "includes/footer.php";

?>