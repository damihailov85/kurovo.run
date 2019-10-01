<?php

$sel = $mysqli->query("SELECT * FROM `run` WHERE `date` < NOW()");

for ($i = 0; $i < $sel->num_rows; $i++){
    $sel->data_seek($i);
    $res = $sel->fetch_assoc();
    $del = $mysqli->query("DELETE FROM `run` WHERE `id` = ".$res['id']);
    $ins = $mysqli->query("INSERT INTO `run_story` (`tg_id`, `date`, `distance`, `description`, `record_time`) VALUES (	'".$res['tg_id']."', '".$res['date']."', '".$res['distance']."', '".$res['description']."', '".$res['record_time']."')");
}

?>