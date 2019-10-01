<?php 
$body = file_get_contents('php://input'); 
$arr = json_decode($body, true); 

if (isset($arr['message'])) {
    $message = $arr['message']['text']; 
    $chat_id = $arr['message']['chat']['id']; 
    $user_id = $arr['message']['from']['id'];
    $name = isset($arr['message']['from']['last_name']) ? $arr['message']['from']['first_name']." ".$arr['message']['from']['last_name'] : $arr['message']['from']['first_name'];
    $username = isset($arr['message']['from']['username']) ? $arr['message']['from']['username'] : '';
    $msg_arr = explode(' ', $message);

    $ins = $mysqli->query("INSERT INTO `log`( `user_id`, `chat_id`, `message`) VALUES ('".$user_id."', '".$chat_id."', '".$message."')");
}


if (isset($arr['message']['photo'])&&in_array($user_id, $admin_list)&&$chat_id==$user_id){
    include('mission/download.php');
}

if ($chat_id==$group_id&&isset($arr['message']['sticker']['file_id'])) {
    $file_id = $arr['message']['sticker']['file_id'];
    $sel = $mysqli->query("SELECT * FROM `stickers` WHERE `sticker_id`='".$file_id."' AND `emotion`='block'");
    if ($sel->num_rows>0) {
        $t = "\xF0\x9F\x98\xA0";
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$t);
        $t2 = "\xF0\x9F\x98\x82";
        file_get_contents('https://api.telegram.org/bot'.$token.'/editMessageText?parse_mode=HTML&message_id='.$arr['message']['message_id'].'&chat_id='.$group_id.'&text='.$t2);
    }
}


$data = '';
if (isset($arr['callback_query'])) {
    $callback_query = $arr['callback_query'];    
    $data = $callback_query['data'];    
    $data_arr = explode(' ', $data);
    $chat_id = $arr['callback_query']['message']['chat']['id'];
    $user_id = $arr['callback_query']['from']['id'];
    $name = $arr['callback_query']['from']['first_name']." ".$arr['callback_query']['from']['last_name'];
    $message_id = $callback_query['message']['message_id'];
    $ins = $mysqli->query("INSERT INTO `log`( `user_id`, `chat_id`, `message`) VALUES ('".$user_id."', '".$chat_id."', '".$data."')");
}

?>