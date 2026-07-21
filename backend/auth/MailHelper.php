<?php
declare(strict_types=1);

require_once APP_ROOT . '/vendor/phpmailer/src/PHPMailer.php';
require_once APP_ROOT . '/vendor/phpmailer/src/SMTP.php';
require_once APP_ROOT . '/vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    public static function sendPasswordReset(string $toName, string $toId, string $resetUrl): bool
    {
        $mail = new PHPMailer(true);

        try {
            if (defined('MAIL_HOST') && MAIL_HOST !== '') {
                $mail->isSMTP();
                $mail->Host       = MAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = MAIL_USERNAME;
                $mail->Password   = MAIL_PASSWORD;
                $mail->SMTPSecure = MAIL_ENCRYPTION;
                $mail->Port       = MAIL_PORT;
                $mail->CharSet    = 'UTF-8';
            }

            $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@localhost';
            $fromName  = defined('APP_NAME') ? APP_NAME : 'FreshJuice Factory';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toId, $toName);

            $mail->isHTML(true);
            $mail->Subject = $fromName . ' - Password Reset';

            $appUrl = defined('APP_URL') ? APP_URL : 'http://localhost/freshjuice';

            $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head><meta charset="UTF-8"></head>
            <body style="margin:0;padding:0;font-family:Inter,system-ui,sans-serif;background:#f1f5f9;">
                <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                    <div style="background:linear-gradient(135deg,#22c55e,#06b6d4);padding:32px;text-align:center;">
                        <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.2);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <span style="font-size:24px;">🔑</span>
                        </div>
                        <h2 style="color:#fff;margin:0;font-size:1.3rem;">Password Reset</h2>
                    </div>
                    <div style="padding:32px;">
                        <p style="color:#334155;font-size:0.9rem;line-height:1.6;">Hello <strong>' . htmlspecialchars($toName) . '</strong>,</p>
                        <p style="color:#334155;font-size:0.9rem;line-height:1.6;">You requested a password reset. Click the button below to set a new password:</p>
                        <div style="text-align:center;margin:28px 0;">
                            <a href="' . htmlspecialchars($resetUrl) . '" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#22c55e,#06b6d4);color:#fff;text-decoration:none;border-radius:10px;font-weight:700;font-size:0.9rem;box-shadow:0 4px 16px rgba(34,197,94,0.3);">Reset My Password</a>
                        </div>
                        <p style="color:#94a3b8;font-size:0.78rem;line-height:1.5;">This link expires in 1 hour. If you didn\'t request this, you can safely ignore this email.</p>
                        <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">
                        <p style="color:#94a3b8;font-size:0.72rem;text-align:center;">' . htmlspecialchars($fromName) . ' &copy; ' . date('Y') . '</p>
                    </div>
                </div>
            </body>
            </html>';

            $mail->AltBody = "Hello {$toName},\n\nYou requested a password reset.\nReset link: {$resetUrl}\n\nThis link expires in 1 hour.\n";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mail send failed: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
