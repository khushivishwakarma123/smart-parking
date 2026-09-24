<?php

session_start();

require_once "config/database.php";

// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}

// Get logged-in user information
$user_name = $_SESSION["user_name"];
$user_email = $_SESSION["user_email"];


// Get total parking slots
$total_query = "SELECT COUNT(*) AS total
                FROM parking_slots";

$total_result = mysqli_query($conn, $total_query);

$total_row = mysqli_fetch_assoc($total_result);

$total_slots = $total_row["total"];


// Get available parking slots
$available_query = "SELECT COUNT(*) AS available
                    FROM parking_slots
                    WHERE status = 'Available'";

$available_result = mysqli_query($conn, $available_query);

$available_row = mysqli_fetch_assoc($available_result);

$available_slots = $available_row["available"];


// Calculate occupied slots
$occupied_slots = $total_slots - $available_slots;


// Get user's total bookings
$booking_query = "SELECT COUNT(*) AS total_bookings
                  FROM bookings
                  WHERE user_id = ?";

$booking_stmt = mysqli_prepare($conn, $booking_query);

mysqli_stmt_bind_param(
    $booking_stmt,
    "i",
    $_SESSION["user_id"]
);

mysqli_stmt_execute($booking_stmt);

$booking_result = mysqli_stmt_get_result($booking_stmt);

$booking_row = mysqli_fetch_assoc($booking_result);

$total_bookings = $booking_row["total_bookings"];

mysqli_stmt_close($booking_stmt);


include "includes/header.php";

?>

<section class="dashboard-section">

    <div class="container">

        <!-- Welcome Section -->

        <div class="dashboard-welcome">

            <p class="dashboard-label">
                USER DASHBOARD
            </p>

            <h1>
                Welcome,
                <?php echo htmlspecialchars($user_name); ?>! 👋
            </h1>

            <p>
                Manage your parking bookings easily from your dashboard.
            </p>

        </div>


        <!-- Parking Statistics -->

        <div class="dashboard-stats">

            <!-- Total Slots -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    🅿️
                </div>

                <h3>
                    Total Slots
                </h3>

                <p class="dashboard-number">
                    <?php echo $total_slots; ?>
                </p>

            </div>


            <!-- Available Slots -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    🟢
                </div>

                <h3>
                    Available Slots
                </h3>

                <p class="dashboard-number">
                    <?php echo $available_slots; ?>
                </p>

            </div>


            <!-- Occupied Slots -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    🔴
                </div>

                <h3>
                    Occupied Slots
                </h3>

                <p class="dashboard-number">
                    <?php echo $occupied_slots; ?>
                </p>

            </div>


            <!-- My Bookings -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    📋
                </div>

                <h3>
                    My Bookings
                </h3>

                <p class="dashboard-number">
                    <?php echo $total_bookings; ?>
                </p>

            </div>

        </div>


        <!-- Quick Actions -->

        <div class="dashboard-actions">

            <h2>
                Quick Actions
            </h2>

            <div class="dashboard-buttons">

                <a href="parking_slots.php"
                   class="dashboard-btn primary">

                    🅿️ Book a Parking Slot

                </a>


                <a href="my_bookings.php"
                   class="dashboard-btn secondary">

                    📋 My Bookings

                </a>

            </div>

        </div>


        <!-- Account Information -->

        <div class="account-section">

            <h2>
                My Account
            </h2>

            <div class="account-info">

                <p>
                    <strong>Name:</strong>

                    <?php
                    echo htmlspecialchars($user_name);
                    ?>
                </p>

                <p>
                    <strong>Email:</strong>

                    <?php
                    echo htmlspecialchars($user_email);
                    ?>
                </p>

            </div>

        </div>


        <!-- Logout -->

        <div class="dashboard-logout">

            <a href="logout.php"
               class="logout-btn">

                Logout

            </a>

        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>