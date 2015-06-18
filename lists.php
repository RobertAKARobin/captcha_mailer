<!DOCTYPE html>
<html>
<head>
<title>E-mail Lists</title>
</head>
<body>
<?php

require("secrets.php");

function decrypt($key, $data){
  $decode = base64_decode($data);
  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $iv_dec = substr($decode, 0, $iv_size);
  $text_dec = substr($decode, $iv_size);
  return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text_dec, MCRYPT_MODE_CBC, $iv_dec);
}

$raw = split("\n", trim(file_get_contents("http://robertgfthomas.com/mail/" . $secret["email"]["list"])));
for($x = count($raw) - 1; $x >= 0; $x--){
  $line = split(",",$raw[$x]);
  $email = decrypt($secret["encrypt"]["key"], $line[1]);
  if(!isset($output[$line[0]]) || !in_array($email, $output[$line[0]])){
    $output[$line[0]][] = $email;
  }
}

foreach($output as $list => $emails){
  echo "<h1>$list</h1>\n";
  echo "<pre>";
  foreach($emails as $email){
    echo "$email\n";
  }
  echo "</pre>";
}

?>
</body>
</html>
