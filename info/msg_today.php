<?php

$sel = $mysqli->query("SELECT COUNT(`log`.`message`) AS `count`,`user`.`name` 
                                FROM `log` JOIN `user` 
                                ON `log`.`tg_id`=`user`.`tg_id` 
                                WHERE 
                                    DATE(`log`.`time`)=DATE(NOW()) AND
                                    `log`.`chat_id`='-2147483648'
                                GROUP BY `log`.`tg_id` 
                                ORDER BY `count` DESC");

if ($sel->num_rows>0) {
    $text = '';
    for($i=0; $i<$sel->num_rows; $i++){
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        $text .= ($i+1).". ".$res['name']." - ".$res['count']."\n";
    }
    
}
else {
    $text = "Пока ничего не написано!?";
}
send_msg($text, $chat_id);

?>