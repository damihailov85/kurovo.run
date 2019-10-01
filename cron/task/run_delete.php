<?php

$sel = $mysqli->query("SELECT * FROM `run` WHERE `date` < NOW()");

for ($i = 0; $i < $sel->num_rows; $i++){
    $sel->data_seek($i);
    $res = $sel->fetch_assoc();
    $del = $mysqli->query("DELETE FROM `run` WHERE `id` = ".$res['id']);
}

?>