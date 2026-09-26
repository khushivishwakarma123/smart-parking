<?php

session_start();

require_once "config/database.php";

// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}

// Get all parking slots
$query = "SELECT id, slot_number, vehicle_type, location, status
          FROM parking_slots
          ORDER BY id ASC";

$result = mysqli_query($conn, $query);

include "includes/header.php";

?>

<section class="parking-slots-section">

    <div class="container">

        <!-- Page Heading -->

        <div class="parking-slots-heading">

            <p class="parking-slots-label">
                SMART PARKING
            </p>

            <h1>
                Parking Slots
            </h1>

            <p>
                View the current availability of all parking slots.
            </p>

        </div>


        <!-- Parking Slots -->

        <div class="slots-grid">

            <?php

            if (mysqli_num_rows($result) > 0) {

                while ($slot = mysqli_fetch_assoc($result)) {

            ?>

                    <div class="slot-card">

                        <!-- Slot Icon -->

                        <div class="slot-icon">
                            🅿️
                        </div>


                        <!-- Slot Number -->

                        <h2>
                            <?php
                            echo htmlspecialchars($slot["slot_number"]);
                            ?>
                        </h2>


                        <!-- Vehicle Type -->

                        <p class="slot-detail">

                            <strong>Vehicle:</strong>

                            <?php
                            echo htmlspecialchars($slot["vehicle_type"]);
                            ?>

                        </p>


                        <!-- Location -->

                        <p class="slot-detail">

                            <strong>Location:</strong>

                            <?php
                            echo htmlspecialchars($slot["location"]);
                            ?>

                        </p>


                        <!-- Status -->

                        <div class="slot-status">

                            <?php

                            if ($slot["status"] == "Available") {

                            ?>

                                <span class="status available">
                                    Available
                                </span>

                            <?php

                            } else {

                            ?>

                                <span class="status occupied">
                                    Occupied
                                </span>

                            <?php

                            }

                            ?>

                        </div>


                        <!-- Action -->

                        <?php

                        if ($slot["status"] == "Available") {

                        ?>
<a
    href="book_slot.php?slot_id=<?php echo $slot["id"]; ?>"
    class="slot-btn available-btn"
>
    Book This Slot
</a>

                        <?php

                        } else {

                        ?>

                            <button
                                type="button"
                                class="slot-btn occupied-btn"
                                disabled
                            >
                                Currently Occupied
                            </button>

                        <?php

                        }

                        ?>

                    </div>

            <?php

                }

            } else {

            ?>

                <div class="no-slots">

                    <h2>
                        No parking slots found.
                    </h2>

                    <p>
                        Please check your database.
                    </p>

                </div>

            <?php

            }

            ?>

        </div>


        <!-- Back to Dashboard -->

        <div class="back-dashboard">

    <a href="check_availability.php" class="btn btn-primary">
        Check Slot Availability
    </a>

    <a href="dashboard.php" class="btn">
        ← Back to Dashboard
    </a>

</div>

    </div>

</section>

<?php

include "includes/footer.php";

?>