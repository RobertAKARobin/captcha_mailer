<?php

require("secrets.php");

function report($success, $message){
  if(isset($_POST["render"]) || isset($_GET["render"])){
    $banner = $success ? "Success! :)" : "Oops!";
    echo <<<HTML
<!DOCTYPE html><html><head><title>Robert AKA Robin</title><link rel="stylesheet" href="../css/business.css" /></head><body><h1>$banner</h1><h2>$message</h2><h2>Feel free to contact <a href="mailto:hello@robertakarobin.com" target="_blank">hello@robertakarobin.com</a> with any questions! Note that my e-mails may show up in your spam or junk folder.</h2></body></html>
HTML;
  }else{
    $code = $success ? 200 : 401;
    http_response_code($code);
    echo(json_encode(array(
      "success" => $success,
      "response" => $message
    )));
  }
  die();
}

function encrypt($key, $data){
  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
  return base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv));
}

$fields = array(
  array("emailMe", "hello@robertakarobin.com"),
  array("fromName", "Robert AKA Robin Thomas"),
  array("emailThem", ""),
  array("subject", ""),
  array("body", "")
);

foreach($fields as $field){
  if(isset($_POST[$field[0]])){
    $$field[0] = $_POST[$field[0]];
  }else if(!empty($field[1])){
    $$field[0] = $field[1];
  }else{
    report(false, "You didn't complete a required field.");
  }
}

$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_URL => "https://www.google.com/recaptcha/api/siteverify",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => array(
    "secret" => $secret["captcha"],
    "response" => $_POST["g-recaptcha-response"],
    "remoteip" => $_SERVER["REMOTE_ADDR"]
  )
));
$response = json_decode(curl_exec($ch), true);
$info = curl_getinfo($ch);
curl_close($ch);
if(!$response["success"]){
  report(false, "CAPTCHA failed!");
}

require "PHPMailerAutoload.php";
$mail = new PHPMailer;    
$mail->isSMTP();
$mail->Host = $secret["email"]["host"];
$mail->SMTPAuth = true;
$mail->Username = "hello@robertakarobin.com";
$mail->Password = $secret["email"]["user"];
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;    
$mail->SetFrom($emailMe, $fromName);
$mail->AddReplyTo($emailMe, $fromName);
$mail->AddAddress($emailThem);
$mail->AddCC($emailMe);
$mail->WordWrap = 5000;
$mail->isHTML(false);
$mail->ContentType = "text/plain";
$mail->Subject = $subject;
$mail->Body = $body . "\n\n" . $_SERVER["REMOTE_ADDR"];
if($mail->send()){
  if(isset($_POST["emailList"])){
    file_put_contents($secret["email"]["list"], "$emailMe," . encrypt($secret["encrypt"]["key"], $emailThem) . PHP_EOL, FILE_APPEND);
    $reportMessage = "Your e-mail has been sent, and you've been added to my mailing list! To unsubscribe, just reply to my e-mail with 'UNSUBSCRIBE'.";
  }else{
    $reportMessage = "Your e-mail has been sent!";
  }
  report(true, $reportMessage);
} else {
  report(false, "My mailer isn't working for some reason. " . $mail->ErrorInfo);
}

?>
