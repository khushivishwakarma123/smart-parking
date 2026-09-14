<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

// Check if login form is submitted
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
        $query = "SELECT id, name, email, password, role
                  FROM users
                  WHERE email = ?";

        // Prepare SQL statement
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            // Bind email
            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $email
            );

            // Execute query
            mysqli_stmt_execute($stmt);

            // Get result
            $result = mysqli_stmt_get_result($stmt);

            // Check whether user exists
            if (mysqli_num_rows($result) == 1) {

                $user = mysqli_fetch_assoc($result);

                // Verify password
                if (password_verify($password, $user["password"])) {

                    // Store user information in session
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["name"];
                    $_SESSION["user_email"] = $user["email"];
                    $_SESSION["user_role"] = $user["role"];

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit;

                } else {

                    $message = "Invalid email or password.";
                    $message_type = "error";
                }

            } else {

                $message = "Invalid email or password.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Database query failed.";
            $message_type = "error";
        }
    }
}

include "includes/header.php";

?>

<section class="login-section">

    <div class="login-container">

        <h1>Welcome Back</h1>

        <p class="login-subtitle">
            Login to book your parking slot
        </p>

        <?php if (!empty($message)) { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <form method="POST" action="login.php">

            <!-- Email -->

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <!-- Password -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- Login Button -->

            <button
                type="submit"
                class="btn login-btn"
            >
                Login
            </button>

        </form>


        <p class="register-link">

            Don't have an account?

            <a href="register.php">
                Create an account
            </a>

        </p>

    </div>

</section>


<?php

include "includes/footer.php";

?>