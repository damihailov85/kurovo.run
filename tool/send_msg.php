<?php 

function send_msg($text, $chat) {

    global $token;
    $url = 'https://api.telegram.org/bot'.$token;
    $method = '/sendMessage?parse_mode=HTML';

    $query = "&chat_id=".$chat."&text=".urlencode($text);
    $result = file_get_contents($url . $method . $query);
   
}


?>