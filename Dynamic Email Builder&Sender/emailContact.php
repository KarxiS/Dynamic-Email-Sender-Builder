<?php

function send_email($to, $from, $subject, $message)
{
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "Od: redacted<" . $from . ">\r\n";
    return mail($to, $subject, $message, $headers);
}

function build_email_template($bgColor, $logoHoreOdkaz, $logoHorePic, $nadpis, $text1, $text2, $text3, $footerLavo1, $footerLavo2, $footerLavo3, $footerLavo4, $footerPravo1, $footerPravo2Odkaz, $footerPravo2Text)
{
    // Get email template as string
    $email_template_string = file_get_contents('emailContact.html', true);
    $email_template_string = str_replace("farbaTable", $bgColor, $email_template_string);
    // Fill email template with message and relevant banner image
    $email_template = sprintf($email_template_string, $logoHoreOdkaz, $logoHorePic, $nadpis, $text1, $text2, $text3, $footerLavo1, $footerLavo2, $footerLavo3, $footerLavo4, $footerPravo1, $footerPravo2Odkaz, $footerPravo2Text);


    return $email_template;
}
#clear unnecessary characters, prevents security breach
function cisticInputu($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    # FIX: Replace this email with recipient email

    $to = "xxxx@xxxx.com";



    # Sender Data
    $name = str_replace(array("\r", "\n"), array(" ", " "), cisticInputu($_POST["name"]));
    $phone = cisticInputu($_POST["phone"]);
    $text = cisticInputu($_POST["message"]);
    $email = cisticInputu($_POST["email"]);
    $localization = cisticInputu($_POST["localization"]);

    if (empty($name) or empty($phone) or empty($text) or empty($email)) {
        # Set a 400 (bad request) response code and exit.
        http_response_code(400);

        if ($localization == 'slovak') {
            echo "Prosím, vyplňte všetky polia";
        } else if ($localization == 'german') {
            echo "Bitte füllen Sie alle Felder aus.";
        } else {
            echo "Please fill in all fields.";

        }

        exit;
    }

    # email headers.
    $headers = "Od: $name ";

    # Mail Content
    $bgColor = "#ac1e1e";
    $logoHoreOdkaz = "https://redacted.sk/";
    $logoHorePic = "https://redacted.sk/logo.png";
    $nadpis = "Nový záujemca <br> " . $name;
    $nadpisMail = "Nový záujemca " . $name;

    $text1 = "Údaje o klientovi:";
    $text2 = "Meno: $name <br>";
    $text2 .= "Telefónne číslo: $phone<br>";
    $text2 .= "Email: $email<br>";

    $text3 = "Správa: $text<br>";

    $footerLavo1 = "redacted.sk";
    $footerLavo2 = "XXXX";
    $footerLavo3 = "XXXXe";
    $footerLavo4 = "+XXXX";
    $footerPravo1 = " ";
    $footerPravo2 = "";
    $footerPravo2Odkaz = "";
    $final_message = build_email_template($bgColor, $logoHoreOdkaz, $logoHorePic, $nadpis, $text1, $text2, $text3, $footerLavo1, $footerLavo2, $footerLavo3, $footerLavo4, $footerPravo1, $footerPravo2Odkaz, $footerPravo2);
    $success = send_email($to, $email, $nadpisMail, $final_message);



    if ($success) {
        # Set a 200 (okay) response code.
        http_response_code(200);
        if ($localization == 'slovak') {
            echo "Ďakujeme. Naši konzultanti vás kontaktujú čo najskôr";
        } else if ($localization == 'german') {
            echo "Dankeschön. Unsere Berater werden Sie so bald wie möglich kontaktieren.";
        } else {
            echo "Thank you. Our consultants will contact you as soon as possible.";

        }

        exit();


    } else {
        # Set a 500 (internal server error) response code.
        http_response_code(500);
        echo "Ups! Nastal problém pri spracovaní, skúste znova neskôr";
        exit();
    }

} else {
    # Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo "Ups! Nastal problém pri spracovaní, skúste znova neskôr";

}


exit;
?>