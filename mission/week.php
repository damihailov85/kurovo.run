<?php

$sel = $mysqli->query("SELECT * FROM `week_mission` ORDER BY `date` DESC");
$sel->data_seek(0);
$res = $sel->fetch_assoc();
$week_mission = $main_page.'img/'.$res['img'];
file_get_contents('https://api.telegram.org/bot'.$token.'/sendPhoto?chat_id='.$user_id.'&photo='.$week_mission);
exit('ok');

?>