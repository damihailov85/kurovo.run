<pre>
<?php

// на вход 
// $squad_url
// $jwt - берется в config.php, рандомом из имеющихся
// $payload - массив! json_encode() здесь
// на выход
// $response = json_decode($file_contents,true);


$squad_url = str_replace("squadrunner.co", "squadeasy.com", $squad_url);

$ch = curl_init($squad_url);

$headers = array(
       "Origin:https://squadeasy.com",
       "Accept-Encoding:gzip, deflate, br",
       "web-api-key:8C4VfqUwTyn0wx2838HWSXQ1WqZO8R2S",
       "Accept-Language:en-US,en;q=0.9,ru;q=0.8,ru-RU;q=0.7",
       "Authorization:".$jwt,
       "Content-Type:application/json",
       "Accept:application/json, text/plain, */*",
       "Referer:https://squadeasy.com/app/en/app/home",
       "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36",
       "Connection:keep-alive");
                                                                
$payload = json_encode($payload); 

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_ENCODING, '');

$file_contents = curl_exec ( $ch );
if (curl_errno ( $ch )) {
    echo "Error:";
    echo curl_error ( $ch );
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text='.curl_error($ch));
    curl_close ( $ch );
    exit ();
}

if ($file_contents=='Unauthorized') {
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Ошибка авторизации в Squad');
}

if (!$file_contents) {
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Squad вернул пустой ответ');
}

curl_close ( $ch );
    
$response = json_decode($file_contents,true);

if (isset($response['err'])) {
    //echo "!!!!!!!!".$squad_url."\n".$file_contents."<br/>";
    send_msg($squad_url."\n".$file_contents , $admin_id);
}



?>

</pre>