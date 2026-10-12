<?php

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config/database.php";

$message = "";
$message_type = "";

// Create a CSRF token for form protection
if (empty($_SESSION["register_csrf_token"])) {
    $_SESSION["register_csrf_token"] = bin2hex(random_bytes(32));
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify the CSRF token
    $submittedToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION["register_csrf_token"], $submittedToken)
    ) {

        $message = "Invalid form submission. Please refresh and try again.";
        $message_type = "error";

    } else {

        // Get form values
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";

        // Validate input types
        if (
            !is_string($name) ||
            !is_string($email) ||
            !is_string($phone) ||
            !is_string($password) ||
            !is_string($confirm_password)
        ) {

            $message = "Invalid form data.";
            $message_type = "error";

        }

        // Check empty fields
        elseif (
            $name === "" ||
            $email === "" ||
            $phone === "" ||
            $password === "" ||
            $confirm_password === ""
        ) {

            $message = "Please fill in all fields.";
            $message_type = "error";

        }

        // Check name length
        elseif (strlen($name) > 100) {

            $message = "Name must not exceed 100 characters.";
            $message_type = "error";

        }

        // Check email format and length
        elseif (
            strlen($email) > 100 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {

            $message = "Please enter a valid email address.";
            $message_type = "error";

        }

        // Check phone number
        elseif (!preg_match("/^[0-9]{10}$/", $phone)) {

            $message = "Phone number must contain exactly 10 digits.";
            $message_type = "error";

        }

        // Check password length
        elseif (strlen($password) < 6) {

            $message = "Password must be at least 6 characters.";
            $message_type = "error";

        }

        // Check password and confirmation
        elseif ($password !== $confirm_password) {

            $message = "Passwords do not match.";
            $message_type = "error";

        }

        else {

            // Check whether the email is already registered
            $check_query = "SELECT id FROM users WHERE email = ?";

            $check_stmt = mysqli_prepare($conn, $check_query);

            if (!$check_stmt) {

                $message = "Unable to process registration. Please try again.";
                $message_type = "error";

            } else {

                mysqli_stmt_bind_param(
                    $check_stmt,
                    "s",
                    $email
                );

                if (!mysqli_stmt_execute($check_stmt)) {

                    $message = "Unable to check your account. Please try again.";
                    $message_type = "error";

                } else {

                    mysqli_stmt_store_result($check_stmt);

                    if (mysqli_stmt_num_rows($check_stmt) > 0) {

                        $message = "Email already registered.";
                        $message_type = "error";

                    } else {

                        // Hash the password before storing it
                        $hashed_password = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                        if ($hashed_password === false) {

                            $message = "Unable to secure your password.";
                            $message_type = "error";

                        } else {

                            // Insert the new user using a prepared statement
                            $insert_query = "INSERT INTO users
                                (name, email, password, phone)
                                VALUES (?, ?, ?, ?)";

                            $insert_stmt = mysqli_prepare(
                                $conn,
                                $insert_query
                            );

                            if (!$insert_stmt) {

                                $message = "Registration failed. Please try again.";
                                $message_type = "error";

                            } else {

                                mysqli_stmt_bind_param(
                                    $insert_stmt,
                                    "ssss",
                                    $name,
                                    $email,
                                    $hashed_password,
                                    $phone
                                );

                                if (mysqli_stmt_execute($insert_stmt)) {

                                    $message = "Registration successful! You can now login.";
                                    $message_type = "success";

                                    // Generate a fresh token after success
                                    $_SESSION["register_csrf_token"] =
                                        bin2hex(random_bytes(32));

                                } else {

                                    // Handle a duplicate email safely
                                    if (mysqli_stmt_errno($insert_stmt) === 1062) {
                                        $message = "Email already registered.";
                                    } else {
                                        $message = "Registration failed. Please try again.";
                                    }

                                    $message_type = "error";
                                }

                                mysqli_stmt_close($insert_stmt);
                            }
                        }
                    }
                }

                mysqli_stmt_close($check_stmt);
            }
        }
    }
}

include "includes/header.php";

?>

<section class="register-section">

    <div class="register-container">

        <h1>Create Your Account</h1>

        <p class="register-subtitle">
            Register to book your parking slot
        </p>

        <?php if ($message !== "") { ?>

            <div class="message <?php echo htmlspecialchars($message_type, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>

        <?php } ?>

        <form method="POST" action="register.php">

            <!-- CSRF protection -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars(
                    $_SESSION["register_csrf_token"],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

            <div class="form-group">

                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    maxlength="100"
                    autocomplete="name"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    maxlength="100"
                    autocomplete="email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="phone">Phone Number</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Enter 10-digit phone number"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    autocomplete="tel"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="confirm_password">Confirm Password</label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>

            <button type="submit" class="btn register-btn">
                Create Account
            </button>

        </form>

        <p class="login-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</section>

<?php

include "includes/footer.php";

?>