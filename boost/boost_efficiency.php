<?php
include('config.php');

$sel = $mysqli->query("SELECT CONCAT(`users`.`first_name`, ' ', `users`.`last_name`) AS `name`, 
                                `users`.`auto` AS `a`,
                                DATEDIFF(MAX(`boost_death_time`), MIN(`boost_death_time`)) AS `days`, 
                                COUNT(`boost_id`) AS `count`, 
                                TIMESTAMPDIFF(minute, MIN(`boost_death_time`),MAX(`boost_death_time`))/((COUNT(`boost_id`)-1)*60*24) AS `coef`, 
                                (TIMESTAMPDIFF(minute, MIN(`boost_death_time`),MAX(`boost_death_time`)) - (COUNT(`boost_id`)-1)*60*24)/(60*24) AS `diff_day`, 
                                (TIMESTAMPDIFF(minute, MIN(`boost_death_time`),MAX(`boost_death_time`)) - (COUNT(`boost_id`)-1)*60*24)/60 AS `diff_hour`, 
                                (TIMESTAMPDIFF(minute, MIN(`boost_death_time`),MAX(`boost_death_time`)) - (COUNT(`boost_id`)-1)*60*24) AS `diff_min` 
                            FROM `boost_story` 
                            JOIN `users` ON `boost_story`.`sender_squad_id`=`users`.`squad_id` 
                            WHERE 1 
                            GROUP BY `boost_story`.`sender_squad_id` 
                            ORDER BY `coef` ASC");


$text = "<i>Среднее время неиспользования буста в часах</i>\n";
$text .= "<i>\xF0\x9F\xA4\x96 - управляется @damihailov85</i>\n\n";

for ($i = 0; $i < $sel->num_rows; $i++){
    $sel->data_seek($i);
    $res = $sel->fetch_assoc();
    

    $text .= $res['name'];
    if ($res['a']==1) $text .= "(\xF0\x9F\xA4\x96)";
    $text .= " - ";
    
    $text .= (($res['coef']-1)*24)."\n";
}

$text = urlencode($text);

file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$text);


?>