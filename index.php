
<?php

require_once "config/database.php";

$query = "SELECT COUNT(*) AS total FROM parking_slots";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

$total_slots = $row["total"];

include "includes/header.php";

?>

<section class="hero">

    <h1>
        Smart Parking Slot Booking
        & Management System
    </h1>

    <p>
        Book your parking slot quickly and easily.
    </p>

    <div class="hero-buttons">

        <a href="login.php" class="btn">
            Login
        </a>

        <a href="register.php" class="btn">
            Register
        </a>

    </div>

</section>


<section class="parking-info">

    <h2>Parking Information</h2>

    <p>
        Total Parking Slots:
        <strong><?php echo $total_slots; ?></strong>
    </p>

</section>


<?php

include "includes/footer.php";

?>