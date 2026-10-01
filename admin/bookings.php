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

if (
    isset($_GET["success"]) &&
    $_GET["success"] == "updated"
) {

    $message = "Booking status updated successfully.";
    $message_type = "success";

}

elseif (
    isset($_GET["error"]) &&
    $_GET["error"] == "failed"
) {

    $message = "Unable to update booking status.";
    $message_type = "error";

}


// =====================================
// GET ALL BOOKINGS
// =====================================

$query = "SELECT
            b.id,
            b.vehicle_number,
            b.booking_date,
            b.start_time,
            b.end_time,
            b.status,
            b.created_at,

            u.name AS user_name,
            u.email AS user_email,

            p.slot_number,
            p.vehicle_type,
            p.location

          FROM bookings b

          INNER JOIN users u
              ON b.user_id = u.id

          INNER JOIN parking_slots p
              ON b.slot_id = p.id

          ORDER BY
              b.booking_date DESC,
              b.start_time DESC,
              b.id DESC";

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
        Manage Bookings - Smart Parking
    </title>

    <link
        rel="stylesheet"
        href="/smart-parking/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/smart-parking/css/admin_bookings.css"
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

<main class="admin-bookings-page">

    <div class="admin-bookings-container">


        <!-- Page Header -->

        <div class="bookings-page-header">

            <div>

                <h1>
                    Manage Bookings
                </h1>

                <p>
                    View and manage all parking bookings.
                </p>

            </div>

        </div>


        <!-- Message -->

        <?php if (!empty($message)) { ?>

            <div
                class="admin-message <?php echo $message_type; ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>


        <!-- Bookings -->

        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ) { ?>

            <div class="admin-bookings-table-container">

                <table class="admin-bookings-table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                User
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
                                Start
                            </th>

                            <th>
                                End
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while (
                            $booking =
                            mysqli_fetch_assoc($result)
                        ) { ?>

                            <tr>


                                <!-- ID -->

                                <td>

                                    #
                                    <?php
                                    echo (int)
                                        $booking["id"];
                                    ?>

                                </td>


                                <!-- USER -->

                                <td>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $booking["user_name"]
                                        );
                                        ?>

                                    </strong>

                                    <br>

                                    <small>

                                        <?php
                                        echo htmlspecialchars(
                                            $booking["user_email"]
                                        );
                                        ?>

                                    </small>

                                </td>


                                <!-- SLOT -->

                                <td>

                                    <strong class="booking-slot">

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


                                <!-- VEHICLE -->

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


                                <!-- DATE -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["booking_date"]
                                    );
                                    ?>

                                </td>


                                <!-- START -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["start_time"]
                                    );
                                    ?>

                                </td>


                                <!-- END -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["end_time"]
                                    );
                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <?php
                                    if (
                                        $booking["status"]
                                        == "Cancelled"
                                    ) {
                                    ?>

                                        <span
                                            class="admin-booking-status cancelled"
                                        >
                                            Cancelled
                                        </span>

                                    <?php
                                    }

                                    elseif (
                                        $booking["status"]
                                        == "Confirmed"
                                    ) {
                                    ?>

                                        <span
                                            class="admin-booking-status confirmed"
                                        >
                                            Confirmed
                                        </span>

                                    <?php
                                    }

                                    else {
                                    ?>

                                        <span
                                            class="admin-booking-status pending"
                                        >
                                            Pending
                                        </span>

                                    <?php
                                    }
                                    ?>

                                </td>


                                <!-- ACTION -->

                                <td>

                                    <form
                                        method="POST"
                                        action="update_booking.php"
                                    >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php
                                            echo (int)
                                                $booking["id"];
                                            ?>"
                                        >


                                        <select
                                            name="status"
                                            class="booking-status-select"
                                            required
                                        >

                                            <option
                                                value="Pending"
                                                <?php
                                                echo $booking["status"]
                                                == "Pending"
                                                ? "selected"
                                                : "";
                                                ?>
                                            >
                                                Pending
                                            </option>

                                            <option
                                                value="Confirmed"
                                                <?php
                                                echo $booking["status"]
                                                == "Confirmed"
                                                ? "selected"
                                                : "";
                                                ?>
                                            >
                                                Confirmed
                                            </option>

                                            <option
                                                value="Cancelled"
                                                <?php
                                                echo $booking["status"]
                                                == "Cancelled"
                                                ? "selected"
                                                : "";
                                                ?>
                                            >
                                                Cancelled
                                            </option>

                                        </select>


                                        <button
                                            type="submit"
                                            class="update-booking-btn"
                                        >
                                            Update
                                        </button>

                                    </form>

                                </td>


                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>


        <?php } else { ?>


            <div class="no-bookings-admin">

                <div class="no-bookings-admin-icon">
                    📋
                </div>

                <h2>
                    No Bookings Found
                </h2>

                <p>
                    There are currently no parking bookings.
                </p>

            </div>


        <?php } ?>


    </div>

</main>







</body>

</html>