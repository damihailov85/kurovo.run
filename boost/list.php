<?php 

$sel_boost = $mysqli->query("SELECT * FROM `squad` JOIN `user` ON `squad`.`tg_id`=`user`.`tg_id` 
                                                    WHERE `squad_id` NOT IN (SELECT `sender_squad_id` FROM `boost`) AND 
                                                        `squad_id`<>0 AND 
                                                        `squad`.`tg_id`>0 AND
                                                        `team`=1");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        $boost[$i] = array('id'=>$res_boost['sender_squad_id'], 'name' => $res_boost['name'],'time' => 0, 'send_to' => 0, 'auto'=> $res_boost['auto']);
    }
}

$sel_boost = $mysqli->query("SELECT `boost`.`boost_death_time`, 
                                    `t`.`auto`, 
                                    `t`.`name`, 
                                    `t`.`squad_id`
                                FROM `boost` 
                                    JOIN 
                                        (SELECT `squad`.`auto`, `user`.`name`, `squad`.`squad_id`, `squad`.`team` 
                                                FROM `squad` 
                                                    JOIN `user` 
                                                    ON `squad`.`tg_id`=`user`.`tg_id`) 
                                        AS `t`
                                    ON `t`.`squad_id`=`boost`.`sender_squad_id` 
                                WHERE `t`.`team`=1 
                                ORDER BY `boost_death_time`");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        
        $time_str = date("H:i", strtotime($res_boost['boost_death_time']));
        $time_str .= date("d", strtotime($res_boost['boost_death_time']))!=date("d") ? "(завтра)" : "";
        
        $boost[count($boost)] = array('id'=>$res_boost['sender_squad_id'], 'name' => $res_boost['name'],'time' => $time_str, 'auto'=> $res_boost['auto']);
    }
}


$text = '';
for ($i = 0; $i<count($boost); $i++) {
    $time = $boost[$i]['time']!='0' ? $boost[$i]['time'] : "<b>Буст готов!</b>";
    if ($boost[$i]['auto']==1&&in_array($user_id, $admin_list)) {
        $text .= "\xF0\x9F\xA4\x96 ";
    }
    $text .= $boost[$i]['name']." ".$time."\n";
}

// $text = urlencode($text);
send_msg($text, $user_id);
exit('ok');
?>