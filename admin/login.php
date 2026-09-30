<?php

session_start();

require_once "../config/database.php";

$message = "";
$message_type = "";


// =====================================
// CHECK ADMIN LOGIN
// =====================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check empty fields
    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";
        $message_type = "error";

    }


    // Check email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }


    else {

        // Find user by email
        $query = "SELECT
                    id,
                    name,
                    email,
                    password,
                    role
                  FROM users
                  WHERE email = ?";

        $stmt = mysqli_prepare(
            $conn,
            $query
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $email
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);


            // Check user
            if (mysqli_num_rows($result) == 1) {

                $user = mysqli_fetch_assoc($result);


                // Check password
                if (password_verify(
                    $password,
                    $user["password"]
                )) {


                    // Check admin role
                    if ($user["role"] === "admin") {

                        // Store admin information
                        $_SESSION["admin_id"] =
                            $user["id"];

                        $_SESSION["admin_name"] =
                            $user["name"];

                        $_SESSION["admin_email"] =
                            $user["email"];

                        $_SESSION["admin_role"] =
                            $user["role"];


                        // Redirect to admin dashboard
                        header(
                            "Location: dashboard.php"
                        );

                        exit;

                    } else {

                        $message =
                            "Access denied. Admin account required.";

                        $message_type =
                            "error";
                    }


                } else {

                    $message =
                        "Invalid email or password.";

                    $message_type =
                        "error";
                }


            } else {

                $message =
                    "Invalid email or password.";

                $message_type =
                    "error";
            }


            mysqli_stmt_close($stmt);

        } else {

            $message =
                "Database query failed.";

            $message_type =
                "error";
        }
    }
}

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
        Admin Login - Smart Parking
    </title>

    <link
        rel="stylesheet"
        href="/smart-parking/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/smart-parking/css/admin.css"
    >

</head>


<body>

<section class="admin-login-section">

    <div class="admin-login-container">


        <div class="admin-icon">
            🛡️
        </div>


        <p class="admin-label">
            SMART PARKING
        </p>


        <h1>
            Admin Login
        </h1>


        <p class="admin-subtitle">
            Login to manage the parking system.
        </p>


        <?php if (!empty($message)) { ?>

            <div
                class="message <?php echo $message_type; ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>


        <form
            method="POST"
            action="login.php"
        >


            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                >

            </div>


            <button
                type="submit"
                class="admin-login-btn"
            >
                Login as Admin
            </button>


        </form>


        <div class="admin-back-link">

            <a href="../login.php">
                ← Back to User Login
            </a>

        </div>


    </div>

</section>

</body>

</html>