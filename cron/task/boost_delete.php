<?php

$sel = $mysqli->query("SELECT `boost`.`id`, `boost`.`sender_squad_id`, `boost`.`recipient_squad_id` , `squad`.`tg_id`
                            FROM `boost` 
                            JOIN `squad` ON `boost`.`recipient_squad_id` = `squad`.`squad_id` 
                            WHERE `boost`.`boost_death_time` < NOW()");

for ($i = 0; $i < $sel->num_rows; $i++){
    $sel->data_seek($i);
    $res = $sel->fetch_assoc();
    
    $del = $mysqli->query("DELETE FROM `boost` WHERE `id` = ".$res['id']);
    
    $query = "SELECT * FROM `squad` WHERE `squad_id` = '".$res['sender_squad_id']."'";
    
    $sel_sender = $mysqli->query($query);

    $sel_sender->data_seek(0);
    $res_sender = $sel_sender->fetch_assoc();

    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$res_sender['tg_id'].'&text=Буст готов!');

}

?>