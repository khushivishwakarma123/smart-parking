
<?php

require_once "config/database.php";

$query = "SELECT COUNT(*) AS total FROM parking_slots";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

$total_slots = $row["total"];

$available_query = "SELECT COUNT(*) AS available 
                    FROM parking_slots 
                    WHERE status = 'Available'";

$available_result = mysqli_query($conn, $available_query);

$available_row = mysqli_fetch_assoc($available_result);

$available_slots = $available_row["available"];

$occupied_slots = $total_slots - $available_slots;

include "includes/header.php";

?>

<!-- Hero Section -->

<section class="hero">

    <div class="hero-content">

        <p class="hero-subtitle">
            SMART PARKING SOLUTION
        </p>

        <h1>
            Find Your Parking Slot
            <span>Quickly & Easily</span>
        </h1>

        <p class="hero-description">
            Book your parking slot in advance and enjoy
            a hassle-free parking experience.
        </p>

        <div class="hero-buttons">

            <a href="login.php" class="btn btn-primary">
                Book a Parking Slot
            </a>

            <a href="#how-it-works" class="btn btn-secondary">
                How It Works
            </a>

        </div>

    </div>

</section>


<!-- Parking Availability -->

<section class="availability">

    <div class="container">

        <div class="section-heading">

            <p class="section-subtitle">
                PARKING STATUS
            </p>

            <h2>
                Parking Availability
            </h2>

            <p>
                Check the current parking slot status.
            </p>

        </div>


        <div class="availability-cards">

            <div class="availability-card">

                <div class="availability-icon">
                    🅿️
                </div>

                <h3>
                    <?php echo $total_slots; ?>
                </h3>

                <p>
                    Total Slots
                </p>

            </div>


            <div class="availability-card">

                <div class="availability-icon">
                    ✅
                </div>

                <h3>
                    <?php echo $available_slots; ?>
                </h3>

                <p>
                    Available Slots
                </p>

            </div>


            <div class="availability-card">

                <div class="availability-icon">
                    🚗
                </div>

                <h3>
                    <?php echo $occupied_slots; ?>
                </h3>

                <p>
                    Occupied Slots
                </p>

            </div>

        </div>

    </div>

</section>


<!-- Features -->

<section class="features">

    <div class="container">

        <div class="section-heading">

            <p class="section-subtitle">
                OUR FEATURES
            </p>

            <h2>
                Why Choose Smart Parking?
            </h2>

        </div>


        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-icon">
                    📱
                </div>

                <h3>
                    Easy Booking
                </h3>

                <p>
                    Book your parking slot easily
                    through our simple online system.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🅿️
                </div>

                <h3>
                    Real-Time Availability
                </h3>

                <p>
                    Check available and occupied
                    parking slots before booking.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🔒
                </div>

                <h3>
                    Secure System
                </h3>

                <p>
                    Your account and booking
                    information is securely managed.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    ⚡
                </div>

                <h3>
                    Quick & Convenient
                </h3>

                <p>
                    Save time by reserving your
                    parking slot before arriving.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- How It Works -->

<section class="how-it-works" id="how-it-works">

    <div class="container">

        <div class="section-heading">

            <p class="section-subtitle">
                SIMPLE PROCESS
            </p>

            <h2>
                How It Works
            </h2>

        </div>


        <div class="steps">

            <div class="step">

                <div class="step-number">
                    1
                </div>

                <h3>
                    Register
                </h3>

                <p>
                    Create your account
                    using your basic details.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    2
                </div>

                <h3>
                    Select Slot
                </h3>

                <p>
                    Check availability and
                    choose your preferred slot.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    3
                </div>

                <h3>
                    Book
                </h3>

                <p>
                    Enter your vehicle details
                    and confirm your booking.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    4
                </div>

                <h3>
                    Park
                </h3>

                <p>
                    Arrive at the parking location
                    and use your reserved slot.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- Call To Action -->

<section class="cta">

    <div class="container">

        <h2>
            Ready to Book Your Parking Slot?
        </h2>

        <p>
            Register now and make parking easier.
        </p>

        <a href="register.php" class="btn btn-primary">
            Get Started
        </a>

    </div>

</section>


<?php

include "includes/footer.php";

?>