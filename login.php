<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config/database.php";

$message = "";
$message_type = "";

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values safely
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Check empty fields
    if ($email === "" || $password === "") {

        $message = "Please enter email and password.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Validate email format
        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        // Find user by email using a prepared statement
        $query = "SELECT id, name, email, password, role
                  FROM users
                  WHERE email = ?";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "s", $email);

            if (mysqli_stmt_execute($stmt)) {

                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) === 1) {

                    $user = mysqli_fetch_assoc($result);

                    // Verify the password against its stored hash
                    if (password_verify($password, $user["password"])) {

                        // Create a new session ID after login
                        session_regenerate_id(true);

                        // Store user information in the session
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["user_name"] = $user["name"];
                        $_SESSION["user_email"] = $user["email"];
                        $_SESSION["user_role"] = $user["role"];

                        // Redirect to the user dashboard
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

                if ($result) {
                    mysqli_free_result($result);
                }

            } else {

                $message = "Unable to process login. Please try again.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Unable to process login. Please try again.";
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

            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <form method="POST" action="login.php">

            <div class="form-group">

                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    autocomplete="email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn login-btn"
            >
                Login
            </button>

        </form>

        <p class="register-link">
            Don't have an account?
            <a href="register.php">Create an account</a>
        </p>

    </div>

</section>

<?php

include "includes/footer.php";

?>