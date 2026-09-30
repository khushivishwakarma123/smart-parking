<?php

session_start();

require_once "config/database.php";

// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";


// =====================================
// SUCCESS / ERROR MESSAGES
// =====================================

// Booking created successfully
if (
    isset($_GET["success"]) &&
    $_GET["success"] == "1"
) {

    $message = "Booking created successfully.";
    $message_type = "success";

}

// Booking cancelled successfully
elseif (
    isset($_GET["cancelled"]) &&
    $_GET["cancelled"] == "1"
) {

    $message = "Booking cancelled successfully.";
    $message_type = "success";

}

// Cancellation failed
elseif (
    isset($_GET["cancel_error"]) &&
    $_GET["cancel_error"] == "1"
) {

    $message = "Unable to cancel the booking.";
    $message_type = "error";

}


// =====================================
// GET LOGGED-IN USER'S BOOKINGS
// =====================================

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

          ORDER BY
              b.booking_date DESC,
              b.start_time DESC";


$stmt = mysqli_prepare(
    $conn,
    $query
);


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


        <!-- =================================
             PAGE HEADING
        ================================== -->

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


        <!-- =================================
             MESSAGE
        ================================== -->

        <?php if (!empty($message)) { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>


        <!-- =================================
             BOOKINGS
        ================================== -->

        <?php if (mysqli_num_rows($result) > 0) { ?>


            <div class="bookings-table-container">

                <table class="bookings-table">


                    <!-- TABLE HEADER -->

                    <thead>

                        <tr>

                            <th>
                                Booking ID
                            </th>

                            <th>
                                Slot
                            </th>

                            <th>
                                Vehicle
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Start Time
                            </th>

                            <th>
                                End Time
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <!-- TABLE BODY -->

                    <tbody>


                        <?php while (
                            $booking =
                            mysqli_fetch_assoc($result)
                        ) { ?>


                            <tr>


                                <!-- Booking ID -->

                                <td>

                                    #
                                    <?php

                                    echo (int)
                                        $booking["id"];

                                    ?>

                                </td>


                                <!-- Slot -->

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


                                <!-- Vehicle -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $booking["vehicle_number"]
                                    );

                                    ?>

                                    <br>

                                    <small>

                                        <?php

                                        echo htmlspecialchars(
                                            $booking["vehicle_type"]
                                        );

                                        ?>

                                    </small>

                                </td>


                                <!-- Date -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $booking["booking_date"]
                                    );

                                    ?>

                                </td>


                                <!-- Start Time -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $booking["start_time"]
                                    );

                                    ?>

                                </td>


                                <!-- End Time -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $booking["end_time"]
                                    );

                                    ?>

                                </td>


                                <!-- Status -->

                                <td>

                                    <?php

                                    if (
                                        $booking["status"]
                                        == "Cancelled"
                                    ) {

                                    ?>

                                        <span
                                            class="booking-status cancelled"
                                        >
                                            Cancelled
                                        </span>

                                    <?php

                                    } else {

                                    ?>

                                        <span
                                            class="booking-status pending"
                                        >

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


                                <!-- ACTION -->

                                <td>


                                    <?php

                                    if (
                                        $booking["status"]
                                        != "Cancelled"
                                    ) {

                                    ?>


                                        <form
                                            method="POST"
                                            action="cancel_booking.php"
                                            onsubmit="return confirm('Are you sure you want to cancel this booking?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="booking_id"
                                                value="<?php
                                                echo (int)
                                                    $booking["id"];
                                                ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="cancel-btn"
                                            >
                                                Cancel
                                            </button>


                                        </form>


                                    <?php

                                    } else {

                                    ?>


                                        <span
                                            class="cancelled-text"
                                        >
                                            Cancelled
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


            <!-- =================================
                 NO BOOKINGS
            ================================== -->

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


        <!-- =================================
             BACK TO DASHBOARD
        ================================== -->

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