<?php
declare(strict_types=1);

/*
 * PHPMailer is optional. It is only present when the project has been installed
 * with Composer (`composer require phpmailer/phpmailer`). Loading it with a bare
 * require_once made every auth route fatal on a fresh checkout -- including the
 * login page -- because vendor/ is gitignored. Load it if it is there, and fall
 * back to PHP's mail() otherwise.
 */
(function (): void {
    foreach ([
        APP_ROOT . '/vendor/autoload.php',
        APP_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
        APP_ROOT . '/vendor/phpmailer/PHPMailer.php',
    ] as $candidate) {
        if (is_file($candidate)) {
            require_once $candidate;
            break;
        }
    }
    foreach ([
        APP_ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php',
        APP_ROOT . '/vendor/phpmailer/SMTP.php',
        APP_ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php',
        APP_ROOT . '/vendor/phpmailer/Exception.php',
    ] as $candidate) {
        if (is_file($candidate)) {
            require_once $candidate;
        }
    }
})();

class MailHelper
{
    /** True when PHPMailer is installed and SMTP credentials are configured. */
    public static function isSmtpAvailable(): bool
    {
        return class_exists(\PHPMailer\PHPMailer\PHPMailer::class)
            && defined('MAIL_HOST') && MAIL_HOST !== '';
    }

    /** True when the app is able to deliver a reset mail by any route. */
    public static function isConfigured(): bool
    {
        return self::isSmtpAvailable()
            || (defined('MAIL_FROM') && MAIL_FROM !== '' && function_exists('mail'));
    }

    public static function sendPasswordReset(string $toName, string $toEmail, string $resetUrl): bool
    {
        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log('Password reset mail skipped: no valid address for ' . $toName);
            return false;
        }

        $fromEmail = defined('MAIL_FROM') && MAIL_FROM !== '' ? MAIL_FROM : 'noreply@localhost';
        $fromName  = defined('APP_NAME') ? APP_NAME : 'FreshJuice Factory';
        $subject   = $fromName . ' - Password Reset';
        $body      = self::buildBody($toName, $resetUrl, $fromName);
        $altBody   = "Hello {$toName},\n\nYou requested a password reset.\nReset link: {$resetUrl}\n\nThis link expires in 1 hour.\n";

        if (self::isSmtpAvailable()) {
            return self::sendViaSmtp($fromEmail, $fromName, $toEmail, $toName, $subject, $body, $altBody);
        }

        return self::sendViaMailFunction($fromEmail, $fromName, $toEmail, $subject, $body);
    }

    private static function sendViaSmtp(
        string $fromEmail, string $fromName, string $toEmail, string $toName,
        string $subject, string $body, string $altBody
    ): bool {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = (int) MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log('Mail send failed: ' . ($mail->ErrorInfo ?: $e->getMessage()));
            return false;
        }
    }

    private static function sendViaMailFunction(
        string $fromEmail, string $fromName, string $toEmail, string $subject, string $body
    ): bool {
        if (!function_exists('mail')) {
            error_log('Mail send skipped: no SMTP configured and mail() unavailable.');
            return false;
        }
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
        ]);
        $sent = @mail($toEmail, $subject, $body, $headers);
        if (!$sent) {
            error_log('Mail send failed via mail() for ' . $toEmail);
        }
        return $sent;
    }

    private static function buildBody(string $toName, string $resetUrl, string $fromName): string
    {
        return '
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
    }
}
