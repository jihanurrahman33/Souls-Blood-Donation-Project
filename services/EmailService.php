<?php
require_once 'config/database.php';

class EmailService {
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $this->fromEmail = SMTP_FROM_EMAIL;
        $this->fromName = 'Souls';
    }
    
    public function sendBloodRequestNotification($bloodGroup, $location, $urgency) {
        $subject = "Urgent Blood Request - $bloodGroup needed in $location";
        $message = $this->getBloodRequestEmailTemplate($bloodGroup, $location, $urgency);
        return $this->sendEmail($subject, $message);
    }
    
    public function sendDonationConfirmationEmail($donorName, $bloodGroup, $donationDate) {
        $subject = "Donation Confirmation - Thank you $donorName!";
        $message = $this->getDonationConfirmationTemplate($donorName, $bloodGroup, $donationDate);
        return $this->sendEmail($subject, $message);
    }
    
    public function sendDonationCompletionEmail($donorName, $recipientName, $bloodGroup) {
        $subject = "Donation Completed - Lives Saved!";
        $message = $this->getDonationCompletionTemplate($donorName, $recipientName, $bloodGroup);
        return $this->sendEmail($subject, $message);
    }
    
    public function sendWelcomeEmail($userName, $userEmail) {
        $subject = "Welcome to Souls!";
        $message = $this->getWelcomeEmailTemplate($userName);
        return $this->sendEmail($subject, $message, $userEmail);
    }
    
    public function sendPasswordResetEmail($userName, $resetToken, $userEmail) {
        $subject = "Password Reset Request - Souls";
        $message = $this->getPasswordResetTemplate($userName, $resetToken);
        return $this->sendEmail($subject, $message, $userEmail);
    }
    
    private function sendEmail($subject, $message, $toEmail = null) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // For development, just log the email instead of actually sending
        // This prevents mail server connection issues
        $this->logEmail($subject, $toEmail ?? 'admin@blooddonation.com', true);
        
        // In production, uncomment the line below and comment out the logEmail line above
        // $result = mail($toEmail ?? 'admin@blooddonation.com', $subject, $message, implode("\r\n", $headers));
        
        return true; // Always return true for development
    }
    
    private function logEmail($subject, $toEmail, $result) {
        $status = $result ? 'SUCCESS' : 'FAILED';
        $logEntry = date('Y-m-d H:i:s') . " | Subject: $subject | To: $toEmail | Result: $status | Mode: DEVELOPMENT\n";
        
        // Ensure logs directory exists
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        
        file_put_contents('logs/emails.log', $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    private function getBloodRequestEmailTemplate($bloodGroup, $location, $urgency) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Souls</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; }
                .urgent { background: #ffc107; color: #333; padding: 10px; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🩸 Souls</h1>
                    <p>Urgent Blood Request</p>
                </div>
                <div class='content'>
                    <div class='urgent'>
                        <h2>🚨 URGENT: $bloodGroup Blood Needed</h2>
                        <p><strong>Location:</strong> $location</p>
                        <p><strong>Urgency:</strong> $urgency</p>
                    </div>
                    <p>Please respond immediately if you can help with this blood donation request.</p>
                    <p>Your donation can save lives!</p>
                </div>
                <div class='footer'>
                    <p>This email was sent from Souls Website</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getDonationConfirmationTemplate($donorName, $bloodGroup, $donationDate) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Souls</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🩸 Souls</h1>
                    <p>Donation Confirmation</p>
                </div>
                <div class='content'>
                    <h2>Thank you, $donorName!</h2>
                    <p>Your blood donation has been confirmed:</p>
                    <ul>
                        <li><strong>Blood Group:</strong> $bloodGroup</li>
                        <li><strong>Donation Date:</strong> $donationDate</li>
                    </ul>
                    <p>Your donation can save up to 3 lives. Thank you for making a difference!</p>
                </div>
                <div class='footer'>
                    <p>Thank you for using Souls!</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getDonationCompletionTemplate($donorName, $recipientName, $bloodGroup) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Souls</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🩸 Souls</h1>
                    <p>Donation Completed</p>
                </div>
                <div class='content'>
                    <h2>🎉 Donation Successfully Completed!</h2>
                    <p><strong>Donor:</strong> $donorName</p>
                    <p><strong>Recipient:</strong> $recipientName</p>
                    <p><strong>Blood Group:</strong> $bloodGroup</p>
                    <p>Your donation has been successfully completed and has helped save a life!</p>
                </div>
                <div class='footer'>
                    <p>Thank you for using Souls!</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getWelcomeEmailTemplate($userName) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Souls</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🩸 Souls</h1>
                    <p>Welcome to Our Community</p>
                </div>
                <div class='content'>
                    <h2>🎉 Welcome to Souls!</h2>
                    <p>Welcome to Souls! We're excited to have you join our community of donors and recipients.</p>
                    <p>With Souls, you can:</p>
                    <ul>
                        <li>Donate blood to help those in need</li>
                        <li>Request blood when you or your loved ones need it</li>
                        <li>Connect with other donors and recipients</li>
                        <li>Track your donation history</li>
                        <li>Join our community forum</li>
                    </ul>
                    <p>Your participation helps save lives every day!</p>
                </div>
                <div class='footer'>
                    <p>Thank you for joining Souls!</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getPasswordResetTemplate($userName, $resetToken) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Souls</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; }
                .button { background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🩸 Souls</h1>
                    <p>Password Reset Request</p>
                </div>
                <div class='content'>
                    <h2>Password Reset Request</h2>
                    <p>Hello $userName,</p>
                    <p>We received a request to reset your password for your Souls account.</p>
                    <p>If you didn't make this request, you can safely ignore this email.</p>
                    <p>To reset your password, click the button below:</p>
                    <p style='text-align: center;'>
                        <a href='" . APP_URL . "auth/reset-password?token=$resetToken' class='button'>Reset Password</a>
                    </p>
                    <p>This link will expire in 1 hour for security reasons.</p>
                </div>
                <div class='footer'>
                    <p>Thank you for using Souls!</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
