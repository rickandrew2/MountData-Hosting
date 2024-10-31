<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

//$mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = "tourismwebiste@gmail.com";
$mail->Password = "rvco kfzi dfns hrbk";

$mail->isHtml(true);

$email = "bonyrazelmorales@gmail.com";

$expiry = date("Y-m-d H:i:s", time() + 60 * 10);

$sql = "UPDATE users
            SET reset_token_hash = ?,
                reset_token_expires_at = ?
            WHERE user_gmail = ?";


$mail->setFrom("tourismwebiste@gmail.com");
$mail->addAddress($email);
$mail->Subject = "I love you";
$mail->Body = <<<END
            <p>QT mo sobra</p>
    END;

try {
    $mail->send();
    echo "<script>showPasswordResetAlert()</script>";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
}