<pre>
<?php

// $squad_url = 'https://api.squadrunner.co/api/v3/runner/runsall/';
// $payload = json_encode(array("tracker_type" => "STRAVA", "user_id" => $uid)); 


if ($my_jwt <> '0') {

    $jwt = $my_jwt;
    $squad_url = 'https://api.squadrunner.co/api/v3/strava/synchronize/';
    $payload = array("user_id" => $squad_id);

    include('squad/request.php');

    $squad_url = 'https://api.squadrunner.co/api/v3/runner/runsall/';
    $payload = array("tracker_type" => "STRAVA", "user_id" => $squad_id);

    include('squad/request.php');

    $last_run_time = date('d.m H:i', strtotime($response[0]['start_time']));
    $last_run_distance = round($response[0]['distance']/1000, 1);

    $text = "Последняя синхронизированная пробежка: ".$last_run_time."\nДистанция: ".$last_run_distance." км\nПопробовать найти ещё? /sync";

  
    send_msg($text, $user_id);

}


?>

</pre>