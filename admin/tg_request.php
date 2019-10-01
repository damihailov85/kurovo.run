<?

include('../tool/config.php');

$sFile = file_get_contents("https://api.telegram.org/bot".$token."/getWebhookInfo"); 

//$sFile = file_get_contents("https://api.telegram.org/bot".$token."/setWebhook?url=https://mda85.s-host.net/kurovo/bot.php");

//$sFile = file_get_contents("https://api.telegram.org/bot".$token."/setWebhook?certificate=1.pem");


echo $sFile;


?>
