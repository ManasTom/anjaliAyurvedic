<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Email configuration
    $to = "dranjalidevi@gmail.com";
    $cc = "anjalidevidr@gmail.com";
    $subject = "Appointment Request from " . $_POST["first_name"] . " " . $_POST["last_name"];
    $message = "First Name: " . $_POST["first_name"] . "\n";
    $message .= "Last Name: " . $_POST["last_name"] . "\n";
    $message .= "Email: " . $_POST["email"] . "\n";
    $message .= "Phone Number: " . $_POST["phone_number"] . "\n";
    $message .= "Treatment: " . $_POST["subject"] . "\n";
    $message .= "Message: " . $_POST["message"];

    // Additional headers
    $headers = "From: " . $_POST["email"] . "\r\n";
    $headers .= "Cc: $cc\r\n";
    $headers .= "Reply-To: " . $_POST["email"] . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Sending email
    if (mail($to, $subject, $message, $headers)) {
        echo "Your appointment request has been sent successfully.";
    } else {
        echo "Failed to send appointment request. Please try again later.";
    }
} else {
    echo "Invalid request.";
}
?>
