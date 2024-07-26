<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = filter_input(INPUT_POST, 'YourName', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'YourPhone', FILTER_SANITIZE_STRING);
    $treatment = filter_input(INPUT_POST, 'treatment', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'YourEmail', FILTER_SANITIZE_EMAIL);
    $date = filter_input(INPUT_POST, 'AppointmentDate', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate input data
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }
    if (empty($treatment)) {
        $errors[] = "Treatment selection is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($date)) {
        $errors[] = "Appointment date is required.";
    }

    if (empty($errors)) {
        // Process the form data (e.g., save to database, send email, etc.)

        // Example: Send an email (update with your email details)
        $to = "info@dranjalisayurveda.com";
        $cc = "anjalidevidr@gmail.com,dranjalisayurveda@gmail.com";
        $Bcc = "edb@illforddigital.com";
        $subject = "DAAC - New Appointment Request from Ad page - Form 1";
        $message = "Name: $name\nPhone: $phone\nTreatment: $treatment\nEmail: $email\nAppointment Date: $date\nMessage: $message";
        $headers = "From: noreply@dranjalisayurveda.com" . "\r\n".
                   "Cc: $cc"."\r\n".
                   "Bcc: $Bcc";

        if (mail($to, $subject, $message, $headers)) {
            // Redirect to thank you page
            header("Location: ad-thankyou.html");
            exit();
        } else {
            echo "Failed to send appointment request.";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>
