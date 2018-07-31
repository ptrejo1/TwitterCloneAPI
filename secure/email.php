<?php

require 'mailgun-php/vendor/autoload.php';
use Mailgun\Mailgun;

class email {

    // generate token
    function generateToken($length) {
        $characters = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $charactersLength = strlen($characters);

        $token = "";
        for ($i = 0; $i < $length; $i++) { $token .= $characters[rand(0, $charactersLength-1)]; }

        return $token;
    }

    // open confirmation template
    function confirmationTemplate() {
        $file = fopen("templates/confirmationTemplate.html", "r") or die("Unable to open file");
        $template = fread($file, filesize("templates/confirmationTemplate.html"));
        fclose($file);

        return $template;
    }

    // send email
    // consider phpmailer in future
    // insert valid credentials
    function sendEmail($details) {
        $mgClient = new Mailgun('9');
        $domain = "";

        $subject = $details["subject"];
        $to = $details["to"];
        $fromName = $details["fromName"];
        $fromEmail = $details["fromEmail"];
        $body = $details["body"];

        $headers = "$fromName <$fromEmail>";

        $result = $mgClient->sendMessage($domain, array(
            'from'      => 'Clone Twitter',
            'to'        => $to,
            'subject'   => $subject,
            'text'      => '',
            'html'      => $body
        ));
    }

    function resetPasswordTemplate() {
        $file = fopen("templates/resetPasswordTemplate.html", "r") or die("Unable to open file");
        $template = fread($file, filesize("templates/resetPasswordTemplate.html"));
        fclose($file);

        return $template;
    }
}