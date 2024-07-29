<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $content = htmlspecialchars($_POST['content']);

    // Create the review submission page content
    $reviewPageContent = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Review Submission</title>
        <style>
            body { background-color: rgb(172, 253, 183); margin: 0; padding: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
            div { width: 100%; background-color: #fff; }
            img { width: 10%; }
            section { width: 96%; height: max-content; margin-top: 2%; border-radius: 10px; background-color: antiquewhite; display: flex; flex-direction: column; align-items: center; justify-content: center; }
            h1 { text-align: center; }
            p { padding: 2%; }
            button { cursor: pointer; background-color: #00ff15a4; padding: 1.5%; border: none; border-radius: 10px; color: #000; font-weight: bold; }
        </style>
        <script src="https://www.gstatic.com/firebasejs/8.4.2/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/8.4.2/firebase-database.js"></script>
    </head>
    <body>
        <div>
            <img onclick="window.open(\'https://dranjalisayurveda.com\')" src="https://dranjalisayurveda.com/assets/img/Group%2025.png" alt="">
        </div>
        <section>
            <h1>You have a new review submission</h1>
            <div style="width: 99%; background-color: rgba(255, 255, 255, 0.658); border: 2px solid green; border-radius: 15px;">
                <p id="review_content">' . $content . '</p>
                <p id="reviewer_name">' . $name . '</p>
                <p id="reviewer_mail">' . $email . '</p>
            </div>
            <div style="width: 80%; display: flex; align-items: center; justify-content: space-around; margin-top: 1%; margin-bottom: 1%;">
                <button onclick="approveReview()">Approve</button>
            </div>
        </section>
        <script>
            var firebaseConfig = {
                apiKey: "AIzaSyBlgiM89yQ3BFA1YYgZijHo4YH2QipjOQ0",
                authDomain: "anjalis-7e2d3.firebaseapp.com",
                databaseURL: "https://anjalis-7e2d3-default-rtdb.firebaseio.com/",
                projectId: "anjalis-7e2d3",
                storageBucket: "anjalis-7e2d3.appspot.com",
                messagingSenderId: "983176713191",
                appId: "1:983176713191:web:2a71795aeb0420c2672342"
            };
            firebase.initializeApp(firebaseConfig);

            function approveReview() {
                var reviewContent = "' . $content . '";
                var reviewerName = "' . $name . '";
                var reviewerMail = "' . $email . '";

                var newReviewRef = firebase.database().ref("Reviews").push();
                newReviewRef.set({
                    content: reviewContent,
                    name: reviewerName,
                    email: reviewerMail,
                    timestamp: Date.now()
                }).then(function () {
                    alert("Review approved and added to the database!");
                }).catch(function (error) {
                    console.error("Error adding review: ", error);
                });
            }
        </script>
    </body>
    </html>';

    // Send the email
    $to = 'info@dranjalisayurveda.com';
    $subject = 'New Review Submission';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <admin@dranjalisayurveda.com>' . "\r\n"; // Replace with your sender email

    if (mail($to, $subject, $reviewPageContent, $headers)) {
        echo 'Review submitted and email sent!';
    } else {
        echo 'There was a problem sending the email.';
    }
} else {
    echo 'Invalid request method.';
}
?>
