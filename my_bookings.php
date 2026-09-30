<?php

session_start();

require_once "config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

// Check success message
if (isset($_GET["success"]) && $_GET["success"] == "1") {

    $message = "Booking created successfully.";
    $message_type = "success";
}


// Get user's bookings
$query = "SELECT
            b.id,
            b.vehicle_number,
            b.booking_date,
            b.start_time,
            b.end_time,
            b.status,
            p.slot_number,
            p.vehicle_type,
            p.location
          FROM bookings b
          INNER JOIN parking_slots p
              ON b.slot_id = p.id
          WHERE b.user_id = ?
          ORDER BY b.booking_date DESC, b.start_time DESC";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

include "includes/header.php";

?>

<section class="my-bookings-section">

    <div class="container">

        <!-- Page Heading -->

        <div class="my-bookings-heading">

            <p class="parking-slots-label">
                SMART PARKING
            </p>

            <h1>
                My Bookings
            </h1>

            <p>
                View and manage your parking bookings.
            </p>

        </div>


        <!-- Message -->

        <?php if (!empty($message)) { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>


        <?php if (mysqli_num_rows($result) > 0) { ?>

            <div class="bookings-table-container">

                <table class="bookings-table">

                    <thead>

                        <tr>

                            <th>Booking ID</th>

                            <th>Slot</th>

                            <th>Vehicle</th>

                            <th>Date</th>

                            <th>Start Time</th>

                            <th>End Time</th>

                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($booking = mysqli_fetch_assoc($result)) { ?>

                            <tr>

                                <td>
                                    #<?php
                                    echo (int) $booking["id"];
                                    ?>
                                </td>

                                <td>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $booking["slot_number"]
                                        );
                                        ?>
                                    </strong>

                                    <br>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            $booking["location"]
                                        );
                                        ?>
                                    </small>

                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $booking["vehicle_number"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $booking["booking_date"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $booking["start_time"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $booking["end_time"]
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php
                                    if ($booking["status"] == "Cancelled") {
                                    ?>

                                        <span class="booking-status cancelled">
                                            Cancelled
                                        </span>

                                    <?php
                                    } else {
                                    ?>

                                        <span class="booking-status pending">
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["status"]
                                            );
                                            ?>
                                        </span>

                                    <?php
                                    }
                                    ?>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <div class="no-bookings">

                <div class="no-bookings-icon">
                    📋
                </div>

                <h2>
                    No Bookings Found
                </h2>

                <p>
                    You have not made any parking bookings yet.
                </p>

                <a
                    href="parking_slots.php"
                    class="btn btn-primary"
                >
                    Book a Parking Slot
                </a>

            </div>

        <?php } ?>


        <!-- Back -->

        <div class="back-dashboard">

            <a
                href="dashboard.php"
                class="btn"
            >
                ← Back to Dashboard
            </a>

        </div>

    </div>

</section>

<?php

mysqli_stmt_close($stmt);

include "includes/footer.php";

?>