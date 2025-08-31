<?php
// Contact form handler
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Sanitize and validate input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get form data
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
$course = isset($_POST['course']) ? sanitizeInput($_POST['course']) : '';
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($course)) {
    $errors[] = 'Course selection is required';
}

// Return errors if any
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Database configuration (update with your credentials)
$db_host = 'localhost';
$db_name = 'hexagon_trading';
$db_user = 'your_username';
$db_pass = 'your_password';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert contact form data
    $stmt = $pdo->prepare("
        INSERT INTO contact_submissions (name, email, phone, course_interest, message, submitted_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$name, $email, $phone, $course, $message]);
    
    // Get the inserted ID
    $submission_id = $pdo->lastInsertId();
    
} catch (PDOException $e) {
    // Log error (in production, log to file instead of displaying)
    error_log("Database error: " . $e->getMessage());
    
    // For now, continue without database (you can modify this behavior)
    $submission_id = null;
}

// Email configuration
$to_email = 'info@hexagontrading.com'; // Change to your email
$subject = 'New Contact Form Submission - Hexagon Trading Institute';

// Email content
$email_content = "
New contact form submission from Hexagon Trading Institute website:

Name: $name
Email: $email
Phone: $phone
Course Interest: $course
Message: $message

Submitted: " . date('Y-m-d H:i:s') . "
";

// Email headers
$headers = [
    'From: noreply@hexagontrading.com',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

// Send email
$email_sent = mail($to_email, $subject, $email_content, implode("\r\n", $headers));

// Prepare response
$response = [
    'success' => true,
    'message' => 'Thank you for your interest! We will contact you soon.',
    'submission_id' => $submission_id
];

// Add email status to response
if (!$email_sent) {
    $response['email_warning'] = 'Form submitted but email notification failed';
    error_log("Failed to send email notification for submission ID: $submission_id");
}

// Send success response
echo json_encode($response);

// Optional: Send auto-reply email to user
$auto_reply_subject = 'Thank you for contacting Hexagon Trading Institute';
$auto_reply_content = "
Dear $name,

Thank you for your interest in Hexagon Trading Institute!

We have received your inquiry about our $course course and will get back to you within 24 hours.

In the meantime, feel free to explore our website and learn more about our trading programs.

Best regards,
The Hexagon Trading Institute Team

---
This is an automated message. Please do not reply to this email.
";

$auto_reply_headers = [
    'From: Hexagon Trading Institute <info@hexagontrading.com>',
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

mail($email, $auto_reply_subject, $auto_reply_content, implode("\r\n", $auto_reply_headers));
?>
