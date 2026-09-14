<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}

include "includes/header.php";

?>

<section class="parking-info">

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </h2>

    <p>
        You are successfully logged in.
    </p>

    <p>
        Email:
        <?php echo htmlspecialchars($_SESSION["user_email"]); ?>
    </p>

    <br>

    <a href="logout.php" class="btn">
        Logout
    </a>

</section>

<?php

include "includes/footer.php";

?>