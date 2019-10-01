<?php

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');

$sel = $mysqli->query("SELECT * FROM `activity_detail` WHERE `distance`/`duration`>10");                                                   

if ($sel->num_rows>0) {
    for ($q = 0; $q < $sel->num_rows; $q++) {
        $sel->data_seek($q);
        $res = $sel->fetch_assoc();

        $sel_run = $mysqli->query("SELECT `distance`*1000/`duration` AS `pace` 
                                        FROM `activity` 
                                        WHERE `activity_id`='".$res['run_id']."'");
        $sel_run->data_seek(0);
        $res_run = $sel_run->fetch_assoc();
        $dist = $res_run['pace']*$res['duration'];

        $mysqli->query("UPDATE `activity_detail` 
                            SET `distance`=".$dist." 
                            WHERE `step_id`='".$res['step_id']."'");
    }
}

$mysqli->close();

?>