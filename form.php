<?php
// =============================================================================
// send-mail.php
// Receives the contact form data and emails it to info@leisureworldcork.com
// =============================================================================

// ── 1. Only allow POST requests ───────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
    exit;
}

// ── 2. Pull in and sanitise each field ───────────────────────────────────────
//    filter_input cleans the data so nobody can inject harmful content.

$fname   = trim(filter_input(INPUT_POST, "fname",   FILTER_SANITIZE_SPECIAL_CHARS));
$lname   = trim(filter_input(INPUT_POST, "lname",   FILTER_SANITIZE_SPECIAL_CHARS));
$email   = trim(filter_input(INPUT_POST, "email",   FILTER_SANITIZE_EMAIL));
$centre  = trim(filter_input(INPUT_POST, "centre",  FILTER_SANITIZE_SPECIAL_CHARS));
$enquiry = trim(filter_input(INPUT_POST, "enquiry", FILTER_SANITIZE_SPECIAL_CHARS));
$message = trim(filter_input(INPUT_POST, "message", FILTER_SANITIZE_SPECIAL_CHARS));

// ── 3. Basic server-side validation ──────────────────────────────────────────
if (!$fname || !$lname || !$email || !$centre || !$enquiry || !$message) {
    echo json_encode(["success" => false, "error" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Invalid email address."]);
    exit;
}

// ── 4. Where the email goes ───────────────────────────────────────────────────
//    Change this if the recipient address ever changes.
$to      = "info@leisureworldcork.com";
$subject = "Website Enquiry – {$enquiry} ({$centre})";

// ── 5. Build the email body ───────────────────────────────────────────────────
$body  = "You have received a new enquiry from the Leisureworld website.\n\n";
$body .= "------------------------------\n";
$body .= "Name:        {$fname} {$lname}\n";
$body .= "Email:       {$email}\n";
$body .= "Centre:      {$centre}\n";
$body .= "Enquiry:     {$enquiry}\n";
$body .= "------------------------------\n\n";
$body .= "Message:\n{$message}\n\n";
$body .= "------------------------------\n";
$body .= "This email was sent from the contact form at leisureworldcork.com\n";

// ── 6. Email headers ──────────────────────────────────────────────────────────
//    Reply-To means when you hit Reply in your inbox it goes back to the visitor.
$headers  = "From: Leisureworld Website <noreply@leisureworldcork.com>\r\n";
$headers .= "Reply-To: {$fname} {$lname} <{$email}>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// ── 7. Send it ────────────────────────────────────────────────────────────────
$sent = mail($to, $subject, $body, $headers);

// ── 8. Tell the browser what happened ────────────────────────────────────────
header("Content-Type: application/json");

if ($sent) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "mail() failed – check server mail settings."]);
}
?>