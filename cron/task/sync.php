<?php

$sel = $mysqli->query("SELECT `sync`.`id`, `sync`.`tg_id`, `squad_id`, `jwt` FROM `sync` JOIN `squad` ON `squad`.`tg_id`=`sync`.`tg_id` WHERE `date` < NOW()");

if ($sel->num_rows>0) {
    for ($i = 0; $i < $sel->num_rows; $i++){
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        if ($res['jwt'] <> '0' && $res['jwt'] <> 0) {
        
            $jwt = $res['jwt'];
            $squad_url = 'https://api.squadrunner.co/api/v3/strava/synchronize/';
            $payload = array("user_id" => $res['squad_id']);
    
            include($path.'squad/request.php');
    
            $squad_url = 'https://api.squadrunner.co/api/v3/runner/runsall/';
            $payload = array("tracker_type" => "STRAVA", "user_id" => $res['squad_id']);

            include('/home/tfbddmzi/public_html/kurovo/squad/request.php');

            $last_run_time = date('d.m H:i', strtotime($response[0]['start_time']));
            $last_run_distance = round($response[0]['distance']/1000, 1);

            $text = "Последняя синхронизированная пробежка: ".$last_run_time."\nДистанция: ".$last_run_distance." км\nПопробовать найти ещё? /sync";
            send_msg($text, $res['tg_id']);

        }

        $del = $mysqli->query("DELETE FROM `sync` WHERE `id` = ".$res['id']);
    }
}

?>