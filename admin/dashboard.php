<?php

session_start();

require_once "../config/database.php";

// =====================================
// CHECK ADMIN SESSION
// =====================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}


// =====================================
// ADMIN INFORMATION
// =====================================

$admin_name = $_SESSION["admin_name"];
$admin_email = $_SESSION["admin_email"];


// =====================================
// TOTAL USERS
// =====================================

$user_query = "SELECT COUNT(*) AS total_users
               FROM users";

$user_result = mysqli_query($conn, $user_query);

$user_row = mysqli_fetch_assoc($user_result);

$total_users = $user_row["total_users"];


// =====================================
// TOTAL PARKING SLOTS
// =====================================

$slot_query = "SELECT COUNT(*) AS total_slots
               FROM parking_slots";

$slot_result = mysqli_query($conn, $slot_query);

$slot_row = mysqli_fetch_assoc($slot_result);

$total_slots = $slot_row["total_slots"];


// =====================================
// AVAILABLE SLOTS
// =====================================

$available_query = "SELECT COUNT(*) AS available_slots
                    FROM parking_slots
                    WHERE status = 'Available'";

$available_result = mysqli_query(
    $conn,
    $available_query
);

$available_row = mysqli_fetch_assoc(
    $available_result
);

$available_slots =
    $available_row["available_slots"];


// =====================================
// TOTAL BOOKINGS
// =====================================

$booking_query = "SELECT COUNT(*) AS total_bookings
                  FROM bookings";

$booking_result = mysqli_query(
    $conn,
    $booking_query
);

$booking_row = mysqli_fetch_assoc(
    $booking_result
);

$total_bookings =
    $booking_row["total_bookings"];


// =====================================
// PENDING BOOKINGS
// =====================================

$pending_query = "SELECT COUNT(*) AS pending_bookings
                  FROM bookings
                  WHERE status = 'Pending'";

$pending_result = mysqli_query(
    $conn,
    $pending_query
);

$pending_row = mysqli_fetch_assoc(
    $pending_result
);

$pending_bookings =
    $pending_row["pending_bookings"];


// =====================================
// CANCELLED BOOKINGS
// =====================================

$cancelled_query = "SELECT COUNT(*) AS cancelled_bookings
                    FROM bookings
                    WHERE status = 'Cancelled'";

$cancelled_result = mysqli_query(
    $conn,
    $cancelled_query
);

$cancelled_row = mysqli_fetch_assoc(
    $cancelled_result
);

$cancelled_bookings =
    $cancelled_row["cancelled_bookings"];

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
        Admin Dashboard - Smart Parking
    </title>

    <link
        rel="stylesheet"
        href="/smart-parking/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/smart-parking/css/admin_dashboard.css"
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
                echo htmlspecialchars($admin_name);
                ?>
            </span>

            <a
                href="/smart-parking/"
                class="admin-site-link"
            >
                User Site
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
     DASHBOARD
===================================== -->

<main class="admin-dashboard">

    <div class="admin-container">


        <!-- Welcome -->

        <div class="admin-welcome">

            <p class="admin-label">
                ADMIN PANEL
            </p>

            <h1>
                Welcome,
                <?php
                echo htmlspecialchars($admin_name);
                ?>!
                👋
            </h1>

            <p>
                Manage your Smart Parking system
                from one place.
            </p>

            <p class="admin-email">

                Logged in as:
                <?php
                echo htmlspecialchars($admin_email);
                ?>

            </p>

        </div>


        <!-- =================================
             STATISTICS
        ================================== -->

        <div class="admin-stats">


            <!-- Users -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    👥
                </div>

                <h3>
                    Total Users
                </h3>

                <p>
                    <?php
                    echo $total_users;
                    ?>
                </p>

            </div>


            <!-- Total Slots -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    🅿️
                </div>

                <h3>
                    Total Slots
                </h3>

                <p>
                    <?php
                    echo $total_slots;
                    ?>
                </p>

            </div>


            <!-- Available -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    🟢
                </div>

                <h3>
                    Available Slots
                </h3>

                <p>
                    <?php
                    echo $available_slots;
                    ?>
                </p>

            </div>


            <!-- Bookings -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    📋
                </div>

                <h3>
                    Total Bookings
                </h3>

                <p>
                    <?php
                    echo $total_bookings;
                    ?>
                </p>

            </div>


            <!-- Pending -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    🟡
                </div>

                <h3>
                    Pending Bookings
                </h3>

                <p>
                    <?php
                    echo $pending_bookings;
                    ?>
                </p>

            </div>


            <!-- Cancelled -->

            <div class="admin-card">

                <div class="admin-card-icon">
                    🔴
                </div>

                <h3>
                    Cancelled Bookings
                </h3>

                <p>
                    <?php
                    echo $cancelled_bookings;
                    ?>
                </p>

            </div>


        </div>


        <!-- =================================
             QUICK ACTIONS
        ================================== -->

        <div class="admin-actions">

            <h2>
                Quick Actions
            </h2>

            <div class="admin-action-grid">

                <a
                    href="slots.php"
                    class="admin-action-card"
                >

                    <span>
                        🅿️
                    </span>

                    <strong>
                        Manage Parking Slots
                    </strong>

                    <small>
                        Add, edit and delete slots
                    </small>

                </a>


                <a
                    href="bookings.php"
                    class="admin-action-card"
                >

                    <span>
                        📋
                    </span>

                    <strong>
                        Manage Bookings
                    </strong>

                    <small>
                        View and manage bookings
                    </small>

                </a>


                <a
                    href="users.php"
                    class="admin-action-card"
                >

                    <span>
                        👥
                    </span>

                    <strong>
                        Manage Users
                    </strong>

                    <small>
                        View registered users
                    </small>

                </a>

            </div>

        </div>


        <!-- =================================
             SYSTEM INFORMATION
        ================================== -->

        <div class="admin-info">

            <h2>
                System Information
            </h2>

            <div class="system-info-grid">

                <div>

                    <strong>
                        Admin
                    </strong>

                    <p>
                        <?php
                        echo htmlspecialchars($admin_name);
                        ?>
                    </p>

                </div>


                <div>

                    <strong>
                        Email
                    </strong>

                    <p>
                        <?php
                        echo htmlspecialchars($admin_email);
                        ?>
                    </p>

                </div>


                <div>

                    <strong>
                        Total Slots
                    </strong>

                    <p>
                        <?php
                        echo $total_slots;
                        ?>
                    </p>

                </div>


                <div>

                    <strong>
                        Available
                    </strong>

                    <p>
                        <?php
                        echo $available_slots;
                        ?>
                    </p>

                </div>

            </div>

        </div>


    </div>

</main>


<!-- =====================================
     FOOTER
===================================== -->

<footer class="admin-footer">

    <p>
        &copy; 2026 Smart Parking
        Slot Booking & Management System
    </p>

</footer>


</body>

</html>