document.addEventListener("DOMContentLoaded", function () {

    // Check email format
    function validEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Display an error message
    function showError(event, message, field) {
        event.preventDefault();
        alert(message);

        if (field) {
            field.focus();
        }
    }


    // =====================================
    // 1. REGISTRATION VALIDATION
    // =====================================

    const confirmPassword = document.getElementById("confirm_password");

    if (confirmPassword) {

        const form = confirmPassword.form;

        form.addEventListener("submit", function (event) {

            const name = document.getElementById("name");
            const email = document.getElementById("email");
            const phone = document.getElementById("phone");
            const password = document.getElementById("password");

            if (!name.value.trim()) {
                showError(event, "Please enter your full name.", name);
                return;
            }

            if (!validEmail(email.value.trim())) {
                showError(event, "Please enter a valid email address.", email);
                return;
            }

            if (!/^[0-9]{10}$/.test(phone.value.trim())) {
                showError(event, "Phone number must contain exactly 10 digits.", phone);
                return;
            }

            if (password.value.length < 6) {
                showError(event, "Password must be at least 6 characters.", password);
                return;
            }

            if (password.value !== confirmPassword.value) {
                showError(event, "Passwords do not match.", confirmPassword);
            }

        });
    }


    // =====================================
    // 2. USER AND ADMIN LOGIN VALIDATION
    // =====================================

    document.querySelectorAll("form").forEach(function (form) {

        const email = form.querySelector('input[name="email"]');
        const password = form.querySelector('input[name="password"]');
        const confirm = form.querySelector('input[name="confirm_password"]');

        // Registration has its own validation
        if (!email || !password || confirm) {
            return;
        }

        form.addEventListener("submit", function (event) {

            if (!validEmail(email.value.trim())) {
                showError(event, "Please enter a valid email address.", email);
                return;
            }

            if (!password.value.trim()) {
                showError(event, "Please enter your password.", password);
            }

        });

    });


    // =====================================
    // 3. BOOKING FORM VALIDATION
    // =====================================

    const vehicleNumber = document.getElementById("vehicle_number");

    if (vehicleNumber) {

        const form = vehicleNumber.form;

        form.addEventListener("submit", function (event) {

            const date = document.getElementById("booking_date");
            const start = document.getElementById("start_time");
            const end = document.getElementById("end_time");

            const vehicle = vehicleNumber.value.trim();

            if (!/^[A-Za-z0-9 -]{4,20}$/.test(vehicle)) {
                showError(
                    event,
                    "Enter a valid vehicle number using 4–20 letters, digits, spaces, or hyphens.",
                    vehicleNumber
                );
                return;
            }

            if (!date.value) {
                showError(event, "Please select a booking date.", date);
                return;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const selectedDate = new Date(date.value + "T00:00:00");

            if (selectedDate < today) {
                showError(event, "Booking date cannot be in the past.", date);
                return;
            }

            if (!start.value || !end.value) {
                showError(event, "Please select both start and end times.", start);
                return;
            }

            if (start.value >= end.value) {
                showError(
                    event,
                    "End time must be later than start time.",
                    end
                );
            }

        });
    }


    // =====================================
    // 4. ADMIN ADD / EDIT SLOT VALIDATION
    // =====================================

    const slotNumber = document.getElementById("slot_number");

    if (slotNumber) {

        const form = slotNumber.form;

        form.addEventListener("submit", function (event) {

            const vehicleType = document.getElementById("vehicle_type");
            const location = document.getElementById("location");

            if (!/^[A-Za-z0-9-]{2,20}$/.test(slotNumber.value.trim())) {
                showError(
                    event,
                    "Slot number must contain 2–20 letters, digits, or hyphens.",
                    slotNumber
                );
                return;
            }

            if (!vehicleType.value) {
                showError(event, "Please select a vehicle type.", vehicleType);
                return;
            }

            if (!location.value.trim()) {
                showError(event, "Please enter the parking location.", location);
            }

        });
    }

});