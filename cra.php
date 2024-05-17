<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-load.php");

$data = $_REQUEST['data'];
$scores = $_REQUEST['scores'];

$test = $_REQUEST['emailaddress'];
if ($test != '') {
    $results = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]/";
    header("Location: $results");
    exit();
}

// SANITIZE INPUT!!!!

$data = json_decode(stripslashes($data), true);
$scores = json_decode(stripslashes($scores), true);


if (!isset($data['contactEmail']) || empty($data['contactEmail'])) {
    $home = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]/";
    header("Location: $home");
    exit();
}

$post_title = random_str(32);

$post_data = array(
    'post_title'  => $post_title,
    'post_type'   => 'cra',
    'post_status' => 'publish'
);
$post_id = wp_insert_post( $post_data );

$dataField = 'field_64955d11c677b';
$scoresField = 'field_649564f0c6785';

update_field($dataField, $data, $post_id);
update_field($scoresField, $scores, $post_id);

$results = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]/cra/$post_title/";

$to = $data['contactEmail'];
$subject = "Afiniti Change Readiness Assessment Tool Results";
$message = <<<EOT
<p>Dear {$data['contactName']},</p>
<p>Thank you for completing the online Afiniti Change Readiness Assessment. You can view and share your results at any time here: <a href="{$results}">{$results}</a>.</p>
<p>If you would like to speak with our team about your change programme, just reply to this email or complete our <a href="http://www.afiniti.co.uk/contact-us/">online enquiry form</a> and we will get back to you.</p>
<p>Best regards,</p>
<p>Afiniti</p>
<img src="data:image/png;base64,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" width="200" height="70">
EOT;
$headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: Afiniti <enquiries@afiniti.co.uk>',
    'Bcc: enquiries@afiniti.co.uk'
);

wp_mail($to, $subject, $message, $headers);

// redirect to results page
header("Location: $results");
exit();