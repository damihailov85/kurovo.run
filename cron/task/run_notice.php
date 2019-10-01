<?php

$sel = $mysqli->query("SELECT `run`.*, `user`.*, `squad`.* , `run`.`id` AS `run_id`
                                FROM `run` 
                                JOIN `user` 
                                    ON `run`.`tg_id`=`user`.`tg_id` 
                                JOIN `squad` 
                                    ON `run`.`tg_id`=`squad`.`tg_id` 
                                WHERE `run`.`date` < NOW() + INTERVAL 2 HOUR AND
                                    `run`.`notice_1h` = 0 ");

if ($sel->num_rows >0) {
    for ($i = 0; $i < $sel->num_rows; $i++) {
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        if (!in_array($res['tg_id'], $to_notice )) {
            $runner_squad = $res['squad_id'];
            $run_date = $res['date'];  
            $runner_name = $res['name'];
            $run_distance = $res['distance'];
            $run_description = $res['description'];
            $link = 0;

            include($path.'run/runner.php');
            $text .= $txt;
            //."\n ______________________ \n";

            $to_notice[count($to_notice)] = $res['tg_id']; 
        }

        $upd = $mysqli->query("UPDATE `run` SET `notice_1h`=1, `notice_start`=1 WHERE `id`=".$res['run_id']);

    }
}    

$sel = $mysqli->query("SELECT `run`.*, `user`.*, `squad`.* , `run`.`id` AS `run_id`
                                FROM `run` 
                                JOIN `user` 
                                    ON `run`.`tg_id`=`user`.`tg_id` 
                                JOIN `squad` 
                                    ON `run`.`tg_id`=`squad`.`tg_id` 
                                WHERE `run`.`notice_start` = 0 ");

if ($sel->num_rows >0) {
    for ($i = 0; $i < $sel->num_rows; $i++) {
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        if (!in_array($res['tg_id'], $to_notice )) {
            $runner_squad = $res['squad_id'];
            $run_date = $res['date'];  
            $runner_name = $res['name'];
            $run_distance = $res['distance'];
            $run_description = $res['description'];
            $link = 0;

            include($path.'run/runner.php');
            $text .= $txt;
            //."\n ______________________ \n";

            $to_notice[count($to_notice)] = $res['tg_id']; 
        }

        $upd = $mysqli->query("UPDATE `run` SET `notice_start`=1 WHERE `id`=".$res['run_id']);
    }
} 
        
?>