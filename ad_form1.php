<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['YourName'];
    $phone = $_POST['YourPhone'];
    $treatment = $_POST['treatment'];
    $email = $_POST['YourEmail'];
    $appointmentDate = $_POST['AppointmentDate'];

    // Recipient email addresses
    $to = "manasom670@gmail.com";
    $cc = "mariasabu0206@gmail.com";
    
    // Email subject
    $subject = "New Appointment Request";

    // Email body
    $message = "
    <html>
    <head>
    <title>New Appointment Request</title>
    </head>
    <body>
    <p>New appointment request details:</p>
    <table>
    <tr><th>Name</th><td>$name</td></tr>
    <tr><th>Phone</th><td>$phone</td></tr>
    <tr><th>Treatment</th><td>$treatment</td></tr>
    <tr><th>Email</th><td>$email</td></tr>
    <tr><th>Appointment Date</th><td>$appointmentDate</td></tr>
    </table>
    </body>
    </html>
    ";

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <$email>" . "\r\n";
    $headers .= "Cc: $cc" . "\r\n";

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Appointment request sent successfully.";
    } else {
        echo "Failed to send appointment request.";
    }
}
?>
