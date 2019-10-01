<?php 

$sel_boost = $mysqli->query("SELECT * FROM `users` WHERE `squad_id` NOT IN (SELECT `sender_squad_id` FROM `boosts`) AND 
                                                        `squad_id`<>0 AND 
                                                        `user_id`>0 AND
                                                        `team`=1");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        $boost[$i] = array('id'=>$res_boost['sender_squad_id'], 'name' => $res_boost['first_name']." ".$res_boost['last_name'],'time' => 0, 'send_to' => 0, 'auto'=> $res_boost['auto']);
    }
}

$sel_boost = $mysqli->query("SELECT `boosts`.*, `users`.`auto`, `users`.`first_name`, `users`.`last_name` FROM `boosts` JOIN `users` ON `users`.`squad_id`=`boosts`.`sender_squad_id` WHERE `users`.`team`=1 ORDER BY `boost_death_time`");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        
        $time_str = date("H:i", strtotime($res_boost['boost_death_time']));
        $time_str .= date("d", strtotime($res_boost['boost_death_time']))!=date("d") ? "(завтра)" : "";
        
        $boost[count($boost)] = array('id'=>$res_boost['sender_squad_id'], 'name' => $res_boost['first_name']." ".$res_boost['last_name'],'time' => $time_str, 'auto'=> $res_boost['auto']);
    }
}


$text = '';
for ($i = 0; $i<count($boost); $i++) {
    $time = $boost[$i]['time']!='0' ? $boost[$i]['time'] : "<b>Буст готов!</b>";
    if ($boost[$i]['auto']==1&&($user_id==412298116||$user_id==145230791||$user_id==120864)) {
        $text .= "\xF0\x9F\xA4\x96 ";
    }
    $text .= $boost[$i]['name']." ".$time."\n";
}

$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$user_id.'&text='.$text);

?>