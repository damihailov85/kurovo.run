<?php

$sel = $mysqli->query("SELECT `squad_id` FROM `squad` WHERE `tg_id`=".$user_id);
$sel->data_seek(0);
$res = $sel->fetch_assoc();

$sel = $mysqli->query("SELECT * FROM `boost` WHERE `sender_squad_id`='".$res['squad_id']."'");
if ($sel->num_rows==1) {
    $sel->data_seek(0);
    $res = $sel->fetch_assoc();
   
    $time = date("d", strtotime($res['boost_death_time']))!=date("d") ? "завтра в " : "в ";
    $time .= date("H:i", strtotime($res['boost_death_time']));

    $text = 'Буст созреет '.$time;
}
else {
    $text = 'Твой буст готов!';
}

send_msg($text, $user_id);
exit('ok');
?>