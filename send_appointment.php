<?php
header('Content-Type: application/json');

// 1. Configuration (CHANGE THESE)
$receiving_email = 'lakkakula.venkatesh8726@gmail.com'; // **CHANGE to the Doctor's Email**
$subject = 'NEW APPOINTMENT REQUEST - Diksha IVF Center';
$from_email = 'no-reply@dikshaivf.in'; // **CHANGE to a working email on your domain**

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get the JSON data sent from the JavaScript fetch request
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// 2. Validate essential fields
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['date'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}

// Sanitize inputs for safety
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);
$date = filter_var($data['date'], FILTER_SANITIZE_STRING);
$service = filter_var($data['service'], FILTER_SANITIZE_STRING) ?: 'Not Specified';
$message = filter_var($data['message'], FILTER_SANITIZE_STRING) ?: 'No additional message.';

// 3. Construct the email body
$email_body = "A new appointment request has been submitted:\n\n";
$email_body .= "Name: " . $name . "\n";
$email_body .= "Email: " . $email . "\n";
$email_body .= "Phone: " . $phone . "\n";
$email_body .= "Preferred Date: " . $date . "\n";
$email_body .= "Requested Service: " . $service . "\n";
$email_body .= "Message: \n" . $message . "\n\n";
$email_body .= "--- \n(Please contact the user using the details above.)";

// 4. Set email headers
$headers = "From: " . $name . " <" . $from_email . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// 5. Send the email and check status
if (mail($receiving_email, $subject, $email_body, $headers)) {
    // Email sent successfully
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} else {
    // Failed to send email (Hostinger issue or configuration error)
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email via server mailer.']);
}

// NOTE: To also integrate Google Sheets, you would add Google Apps Script code here
// or use a more robust backend service that integrates with the Google Sheets API.
?>