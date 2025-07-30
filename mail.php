<?php
function send_verification_email($to, $code) {
    $subject = "Your BodyLift Verification Code";
    
    $message = "Hello,\n\nThank you for signing up for Bodylift.\n\n";
    $message .= "Your verification code is: $code\n\n";
    $message .= "Please enter this code to verify your account.\n\n";
    $message .= "Regards,\nBodyLift Team";

    $headers = "From: BodyLift <no-reply@yourdomain.com>\r\n";
    $headers .= "Reply-To: no-reply@yourdomain.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    file_put_contents("mail_debug.log", "Sending to: $to\nCode: $code\nHeaders: $headers\n", FILE_APPEND);

    return mail($to, $subject, $message, $headers);
}
?>
