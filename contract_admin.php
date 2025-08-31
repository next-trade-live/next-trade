<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form fields collect karo
    $full_name   = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $cnic        = $_POST['cnic'];
    $dob         = $_POST['dob'];
    $contact     = $_POST['contact'];
    $email       = $_POST['email'];
    $city        = $_POST['city'];
    $course      = $_POST['course'];

    // Arrays handle karo (languages, mode, package, payment)
    $language    = isset($_POST['language']) ? implode(", ", $_POST['language']) : "Not selected";
    $mode        = isset($_POST['mode']) ? implode(", ", $_POST['mode']) : "Not selected";
    $package     = isset($_POST['package']) ? implode(", ", $_POST['package']) : "Not selected";
    $amount      = $_POST['amount'];
    $payment     = isset($_POST['payment']) ? implode(", ", $_POST['payment']) : "Not selected";
    $transaction = $_POST['transaction'];

    // Email settings
    $to = "mifitnesssubscription@gmail.com"; // 👈 Yahan apni email
    $subject = "🎓 New Admission Form Submission - $full_name";

    $message = "
    <h2>New Admission Form Submission</h2>
    <p><strong>Full Name:</strong> $full_name</p>
    <p><strong>Father's Name:</strong> $father_name</p>
    <p><strong>CNIC:</strong> $cnic</p>
    <p><strong>Date of Birth:</strong> $dob</p>
    <p><strong>Contact:</strong> $contact</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>City:</strong> $city</p>
    
    <h3>Course Information</h3>
    <p><strong>Course:</strong> $course</p>
    <p><strong>Preferred Language:</strong> $language</p>
    <p><strong>Mode of Learning:</strong> $mode</p>
    
    <h3>Payment Details</h3>
    <p><strong>Package:</strong> $package</p>
    <p><strong>Amount Paid:</strong> Rs. $amount</p>
    <p><strong>Payment Method:</strong> $payment</p>
    <p><strong>Transaction ID:</strong> $transaction</p>
    ";

    // Headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Admission Form <no-reply@yourdomain.com>" . "\r\n";

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "<h2 style='color:green; text-align:center;'>✅ Admission form submitted successfully! We will contact you soon.</h2>";
    } else {
        echo "<h2 style='color:red; text-align:center;'>❌ Failed to send admission form. Please try again.</h2>";
    }
}
?>
