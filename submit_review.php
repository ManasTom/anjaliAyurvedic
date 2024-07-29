<?php
    // Email configuration
    $to = "info@dranjalisayurveda.com,anjalidevidr@gmail.com";
    $subject = "New review submission";

    $message = '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                padding: 10px 0;
                background-color: #e5f6e5;
                border-radius: 10px 10px 0 0;
            }
            .header img {
                max-width: 100px;
            }
            .content {
                padding: 20px;
                color: #333333;
                line-height: 1.5;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                color: #ffffff;
                background-color: #5cb85c;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 20px;
            }
            .footer {
                text-align: center;
                padding: 10px;
                font-size: 12px;
                color: #999999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="https://dranjalisayurveda.com/assets/img/Group%2025.webp" alt="Ayurveda Center Logo">
            </div>
            <div class="content">
                <h2>New Review Submission</h2>
                <p>You have a new review submission.</p>
                <p>Click the link below to access the dashboard:</p>
                <a href="https://dranjalisayurveda.com/admin.html" class="button">Access Dashboard</a>
            </div>
            <div class="footer">
                <p>&copy; 2024 Ayurveda Center. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    // Additional headers for the HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Admin <admin@dranjalisayurveda.com>" . "\r\n";
    $headers .= "Reply-To: Admin <admin@dranjalisayurveda.com>" . "\r\n";

    // Sending email to the main recipient
    if (mail($to, $subject, $message, $headers)) {
        echo "<script type='text/javascript'>alert('Your message has been sent successfully.');window.location.href = 'index.html';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Failed to send message. Please try again later.');window.location.href = 'index.html';</script>";
    }
?>
