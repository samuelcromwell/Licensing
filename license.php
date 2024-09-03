<?php
// license.php

require 'db.php';
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set the header for JSON response
header('Content-Type: application/json');

// Function to generate a random license key
function generateLicenseKey($length = 16) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $licenseKey = '';
    for ($i = 0; $i < $length; $i++) {
        $licenseKey .= $characters[rand(0, $charactersLength - 1)];
    }
    return $licenseKey;
}

// Function to send the license key to the user's email
function sendLicenseKeyEmail($to, $licenseKey) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'cromwellsamuel3@gmail.com'; // SMTP username
        $mail->Password = 'ldot jehk yuuo luhf'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('cromwellsamuel3@gmail.com', 'Licensing');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Your License Key';
        $mail->Body = "<p>Thank you for your purchase. Your license key is: <strong>$licenseKey</strong></p>";
        $mail->AltBody = "Thank you for your purchase. Your license key is: $licenseKey";

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_license'])) {
        // Check if the license key exists in the database and is active
        $stmt = $pdo->prepare("SELECT status, expiry_date FROM llx_licencing_table WHERE status = 'Active'");
        $stmt->execute();
        $license = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($license) {
            echo json_encode(['success' => true, 'status' => $license['status'], 'expiry_date' => $license['expiry_date']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active license found.']);
        }
        exit;
    }

    if (isset($_POST['generate_license'])) {
        // Capture form data
        $phoneNumber = trim($_POST['phone_number']);
        $emailAddress = trim($_POST['email_address']);

        if (empty($phoneNumber) || empty($emailAddress)) {
            echo json_encode(['success' => false, 'message' => 'Phone number and email address cannot be empty.']);
            exit;
        }

        // Generate a new license key
        $generatedLicenseKey = generateLicenseKey();
        $expiryDate = date('Y-m-d H:i:s', strtotime('+1 year'));
        $status = 'Active';
        
        // Insert the new license key into the database
        $stmt = $pdo->prepare("INSERT INTO llx_licencing_table (license_key, status, expiry_date) VALUES (?, ?, ?)");
        if ($stmt->execute([$generatedLicenseKey, $status, $expiryDate])) {
            // Send the generated license key via email
            sendLicenseKeyEmail($emailAddress, $generatedLicenseKey);

            // Provide feedback to the user
            echo json_encode(['success' => true, 'message' => 'License key generated and sent to the provided email address.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate license key.']);
        }
        exit;
    }

    $licenseKey = trim($_POST['license_key']);

    if (empty($licenseKey)) {
        echo json_encode(['success' => false, 'message' => 'License key cannot be empty.']);
        exit;
    }

    // Remove Keygen validation since we're generating our own keys
    // Now directly check if the license key exists in your database
    $stmt = $pdo->prepare("SELECT status, expiry_date FROM llx_licencing_table WHERE license_key = ?");
    $stmt->execute([$licenseKey]);
    $license = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($license) {
        echo json_encode(['success' => true, 'expiry_date' => $license['expiry_date'], 'license_key' => $licenseKey, 'status' => $license['status']]);
    } else {
        // Generate a new license key since it doesn't exist in the database
        $generatedLicenseKey = generateLicenseKey();
        $expiryDate = date('Y-m-d H:i:s', strtotime('+1 year'));
        $status = 'Active';
        $stmt = $pdo->prepare("INSERT INTO llx_licencing_table (license_key, status, expiry_date) VALUES (?, ?, ?)");

        if ($stmt->execute([$generatedLicenseKey, $status, $expiryDate])) {
            // Send the generated license key via email
            sendLicenseKeyEmail($emailAddress, $generatedLicenseKey);

            echo json_encode(['success' => true, 'expiry_date' => $expiryDate, 'license_key' => $generatedLicenseKey, 'status' => $status]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert license key.']);
        }
    }
}
?>
