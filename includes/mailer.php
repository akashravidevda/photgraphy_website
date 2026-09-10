<?php
/**
 * Swamini Photography - Mailer Service
 * Supports PHPMailer / native mail() with secure headers and responsive HTML templates
 */

require_once __DIR__ . '/config.php';

class SwaminiMailer {

    /**
     * Send email using configured method (SMTP or native mail)
     */
    public static function sendMail(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool {
        // Strip line breaks from subject and addresses to prevent email header injection
        $subject = str_replace(["\r", "\n"], '', $subject);
        $toEmail = filter_var($toEmail, FILTER_SANITIZE_EMAIL);
        $toName  = str_replace(["\r", "\n", '"'], '', $toName);

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Plaintext fallback if not provided
        if (empty($textBody)) {
            $textBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));
        }

        if (defined('USE_SMTP') && USE_SMTP === true && file_exists(__DIR__ . '/../vendor/PHPMailer/PHPMailer.php')) {
            try {
                require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
                require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
                require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port       = SMTP_PORT;

                $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
                $mail->addAddress($toEmail, $toName);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlBody;
                $mail->AltBody = $textBody;
                $mail->CharSet = 'UTF-8';

                return $mail->send();
            } catch (\Exception $e) {
                error_log("PHPMailer Error: " . $e->getMessage());
                // Fall back to native mail
            }
        }

        // Standard Secure Native Mail Function
        $boundary = "==Multipart_Boundary_x" . md5(time()) . "x";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

        $message  = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $textBody . "\r\n\r\n";

        $message .= "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";
        $message .= "--$boundary--";

        return @mail($toEmail, $subject, $message, $headers);
    }

    /**
     * Generate HTML template for Admin Notification
     */
    public static function buildAdminNotificationEmail(array $data): string {
        $name      = htmlspecialchars($data['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $phone     = htmlspecialchars($data['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $email     = htmlspecialchars($data['email'] ?? 'Not provided', ENT_QUOTES, 'UTF-8');
        $service   = htmlspecialchars($data['service_label'] ?? 'General Enquiry', ENT_QUOTES, 'UTF-8');
        $eventDate = htmlspecialchars($data['event_date'] ?? 'Flexible / Unspecified', ENT_QUOTES, 'UTF-8');
        $message   = nl2br(htmlspecialchars($data['message'] ?? 'None', ENT_QUOTES, 'UTF-8'));
        $time      = date('d M Y, h:i A');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f8f2ea; color: #171514; margin: 0; padding: 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e9e2db; }
        .header { background: #171514; color: #f8f2ea; padding: 24px; text-align: center; }
        .header h1 { margin: 0 0 6px 0; font-size: 20px; letter-spacing: 1px; color: #d7b98a; }
        .header p { margin: 0; font-size: 13px; color: #81766e; }
        .content { padding: 30px; }
        .badge { display: inline-block; background: #f8f2ea; color: #b58a58; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 13px; margin-bottom: 20px; border: 1px solid #d7b98a; }
        .detail-row { margin-bottom: 14px; border-bottom: 1px solid #f0eae1; padding-bottom: 10px; }
        .label { font-size: 11px; text-transform: uppercase; color: #81766e; font-weight: 600; letter-spacing: 0.5px; }
        .val { font-size: 15px; color: #171514; margin-top: 3px; font-weight: 500; }
        .message-box { background: #fcfbfa; border: 1px solid #e9e2db; border-radius: 6px; padding: 16px; margin-top: 20px; font-size: 14px; line-height: 1.6; }
        .actions { margin-top: 25px; text-align: center; }
        .btn-call { display: inline-block; background: #b58a58; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; font-size: 13px; margin: 0 5px; }
        .btn-whatsapp { display: inline-block; background: #25D366; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; font-size: 13px; margin: 0 5px; }
        .footer { background: #f8f2ea; color: #81766e; text-align: center; padding: 16px; font-size: 12px; border-top: 1px solid #e9e2db; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>SWAMINI PHOTOGRAPHY</h1>
            <p>New Client Booking Enquiry</p>
        </div>
        <div class="content">
            <span class="badge">Service: {$service}</span>
            <div class="detail-row">
                <div class="label">Client Name</div>
                <div class="val">{$name}</div>
            </div>
            <div class="detail-row">
                <div class="label">Phone Number</div>
                <div class="val"><a href="tel:{$phone}" style="color: #171514; text-decoration: none; font-weight: bold;">{$phone}</a></div>
            </div>
            <div class="detail-row">
                <div class="label">Email Address</div>
                <div class="val">{$email}</div>
            </div>
            <div class="detail-row">
                <div class="label">Target Event / Shoot Date</div>
                <div class="val">{$eventDate}</div>
            </div>
            <div class="message-box">
                <div class="label" style="margin-bottom: 6px;">Client Message / Requirements</div>
                <div>{$message}</div>
            </div>
            <div class="actions">
                <a href="tel:{$phone}" class="btn-call">Call Client</a>
                <a href="https://wa.me/91{$phone}" class="btn-whatsapp">Chat on WhatsApp</a>
            </div>
        </div>
        <div class="footer">
            Received on {$time} via Swamini Photography Website
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Generate HTML template for Customer Confirmation Email
     */
    public static function buildCustomerConfirmationEmail(array $data): string {
        $name    = htmlspecialchars($data['name'] ?? 'there', ENT_QUOTES, 'UTF-8');
        $service = htmlspecialchars($data['service_label'] ?? 'photography shoot', ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f8f2ea; color: #171514; margin: 0; padding: 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e9e2db; }
        .header { background: #171514; color: #f8f2ea; padding: 30px; text-align: center; }
        .header h1 { margin: 0 0 6px 0; font-size: 22px; letter-spacing: 2px; color: #d7b98a; }
        .header p { margin: 0; font-size: 13px; color: #81766e; font-style: italic; }
        .content { padding: 30px; line-height: 1.7; font-size: 15px; }
        .highlight { color: #b58a58; font-weight: 600; }
        .contact-box { background: #f8f2ea; border: 1px solid #d7b98a; border-radius: 6px; padding: 20px; margin: 25px 0; text-align: center; }
        .contact-btn { display: inline-block; background: #25D366; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold; font-size: 14px; margin-top: 10px; }
        .footer { background: #171514; color: #81766e; text-align: center; padding: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>SWAMINI</h1>
            <p>Photography & Cinematography</p>
        </div>
        <div class="content">
            <p>Hello <strong>{$name}</strong>,</p>
            <p>Thank you for reaching out to <strong>Swamini Photography & Cinematography</strong> regarding your <span class="highlight">{$service}</span>.</p>
            <p>We have safely received your enquiry. Navanit Patil and our team will review your details and get back to you shortly to discuss your ideas, availability, and tailored package options.</p>
            
            <div class="contact-box">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #171514;"><strong>Need an instant consultation or urgent date check?</strong></p>
                <a href="https://wa.me/918432582511?text=Hi%20Navanit,%20I%20just%20submitted%20an%20enquiry%20for%20{$service}." class="contact-btn">Message Us on WhatsApp (+91 8432582511)</a>
            </div>
            
            <p>We look forward to creating timeless memories for you.</p>
            <p>Warm regards,<br>
            <strong>Navanit Patil & Team Swamini</strong><br>
            <em>"Your Moments. Our Passion. Memories Forever."</em></p>
        </div>
        <div class="footer">
            &copy; 2026 Swamini Photography & Cinematography. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML;
    }
}
