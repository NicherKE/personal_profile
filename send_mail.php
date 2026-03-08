<?php
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Point to the location of your PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(403);
    echo "Access denied. Only POST requests are allowed.";
    exit;
}

// --- 1. Get and Sanitize Input Data ---
$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$message = trim($_POST["message"]);

// --- 2. Basic Validation ---
if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Please ensure all fields are completed and the email address is valid.";
    exit;
}

// --- 3. PHPMailer Initialization ---
$mail = new PHPMailer(true);

try {
    // --- 4. SMTP Configuration (Use YOUR service details) ---
    // Enable verbose debug output (for testing/troubleshooting only!)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    $mail->isSMTP(); // Send using SMTP
    $mail->Host       = 'smtp.example.com'; // **CRITICAL: Set the SMTP server to send through**
    $mail->SMTPAuth   = true;               // Enable SMTP authentication
    $mail->Username   = 'your_smtp_username'; // **CRITICAL: SMTP username (usually your email address)**
    $mail->Password   = 'your_smtp_password'; // **CRITICAL: SMTP password (or app password)**
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption (or 'tls' for 587)
    $mail->Port       = 465; // TCP port to connect to; use 587 if you set `SMTPSecure = 'tls'`

    // --- 5. Sender and Recipient Settings ---
    $mail->setFrom('your_smtp_username', 'Contact Form Sender'); // Must match $mail->Username for some hosts
    $mail->addAddress('YOUR_RECIPIENT_EMAIL@example.com', 'Recipient Name'); // **CRITICAL: Add a recipient**
    $mail->addReplyTo($email, $name); // Set the user's email as the reply-to address

    // --- 6. Content ---
    $mail->isHTML(false); // Set email format to plain text
    $mail->Subject = 'New Contact Form Submission from ' . $name;
    
    $body = "Name: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Message:\n$message\n";
    
    $mail->Body = $body;

    // --- 7. Send the Email ---
    $mail->send();
    
    // Success response
    http_response_code(200);
    echo "Thank You! Your message has been sent successfully.";

} catch (Exception $e) {
    // Failure response
    http_response_code(500);
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>