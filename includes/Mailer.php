<?php
namespace MailMan;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private Config $config;
    private Logger $logger;

    public function __construct() {
        $this->config = new Config();
        $this->logger = new Logger();
    }

    public function send(array $params): array {
        $mail = new PHPMailer(true);

        try {
            $smtp = $this->config->getSMTP();

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = $smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp['port'];
            $mail->CharSet = 'UTF-8';

            // Sender
            $mail->setFrom($smtp['from_email'], $params['from_name'] ?? $smtp['from_name']);

            // Recipient
            $mail->addAddress($params['to_email'], $params['to_name'] ?? '');

            // Content
            $mail->isHTML($params['is_html'] ?? true);
            $mail->Subject = $params['subject'];
            $mail->Body = $params['body'];

            // Attachments
            if (!empty($params['attachments'])) {
                foreach ($params['attachments'] as $attachment) {
                    $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
                }
            }

            $mail->send();

            // Log success
            $this->logger->log([
                'status' => 'success',
                'to_email' => $params['to_email'],
                'to_name' => $params['to_name'] ?? '',
                'subject' => $params['subject'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return ['success' => true, 'message' => 'Email berhasil dikirim'];

        } catch (Exception $e) {
            // Log failure
            $this->logger->log([
                'status' => 'failed',
                'to_email' => $params['to_email'],
                'subject' => $params['subject'],
                'error' => $mail->ErrorInfo,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return ['success' => false, 'message' => 'Email gagal dikirim: ' . $mail->ErrorInfo];
        }
    }

    public function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
