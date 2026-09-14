<?php

require_once "config/database.php";

$message = "";
$message_type = "";

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

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify hashed password
            if (password_verify($password, $user["password"])) {

                $message = "Login successful!";
                $message_type = "success";

            } else {

                $message = "Invalid email or password.";
                $message_type = "error";
            }

        } else {

            $message = "Invalid email or password.";
            $message_type = "error";
        }

        mysqli_stmt_close($stmt);
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


            <button type="submit" class="btn login-btn">
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