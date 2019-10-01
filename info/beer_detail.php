<?php 

$sel = $mysqli->query("SELECT `j`.`n` AS `nm`, 
                                COUNT(`activity`.`distance`) AS `count` 
                            FROM `activity` 
                            JOIN (SELECT `user`.`tg_id` AS `t`, `user`.`name` AS `n`, `squad`.`squad_id` AS `s` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id` WHERE `squad`.`team`=1) AS `j`
                                ON `activity`.`squad_id`=`j`.`s` 
                            WHERE `activity`.`activity`='running' AND
                                WEEK(`activity`.`date` - INTERVAL 1 DAY) = WEEK(NOW() - INTERVAL 1 DAY) AND
                                YEAR(`activity`.`date`) = YEAR(NOW())
                            GROUP BY `j`.`n` 
                            ORDER BY `count` DESC");                
                            
$list = "";
$run = 0;

if ($sel->num_rows>0) {
    for ($i = 0; $i < $sel->num_rows; $i++) {
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        
        $count = ($res['count']>3) ? 3 : $res['count'];
        $run += $count;
        $list .= "<b>".$count."</b>  ".$res['nm']."\n";
    }
}

$text = $run . "/60\n\n".$list;

$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);

?>