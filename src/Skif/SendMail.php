<?php

namespace Skif;


class SendMail
{
    public static function mailToUtf8($to, $from_mail, $from_name, $subject, $message)
    {
        $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: " . $from_name . " <" . $from_mail . ">\r\n";

        return mail($to, $subject, $message, $headers);
    }

    public static function mailWithAttachment($to, $from_mail, $from_name, $subject, $message, $filename, $file_output)
    {
        $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $boundary = "--" . md5(uniqid(time()));

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $from_name . " <" . $from_mail . ">\r\n";
        $headers .= "Reply-To: $from_mail\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $body = "--$boundary\n";

        $body .= "Content-type: text/html; charset=utf-8\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\n\n";
        $body .= $message . "\n\n";

        $body .= "--$boundary\n";

        $body .= "Content-Type: application/octet-stream; name==?utf-8?B?" . base64_encode($filename) . "?=\n";
        $body .= "Content-Transfer-Encoding: base64\n";
        $body .= "Content-Disposition: attachment; filename==?utf-8?B?" . base64_encode($filename) . "?=\n\n";
        $body .= chunk_split(base64_encode($file_output)) . "\n";

        $body .= "--" . $boundary . "--\n";

        return mail($to, $subject, $body, $headers);
    }
}