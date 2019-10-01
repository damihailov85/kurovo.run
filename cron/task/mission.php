<?php

$squad_url = 'https://api.squadrunner.co/api/v3/missions/missionrunner/';

$payload = array(
    "company_id"=> "5a1554b82c1350cbd9afbade",
    "user_id" => $squad_id_to_curl
); 

$jwt = $jwt_to_curl;

include($path.'squad/request.php');
        
$mission_id = $response['mission']['_id'];
$text = "Сегодняшняя миссия: \n".$response['mission']['name']."\n".$response['mission']['reward']." points";

if ($response['mission']['missionTemplate_id']['mode']!="solo"){
    $text .= "\nКомандная";
}

$start_date = date("U", strtotime($response['mission']['start_date']));
$end_date = date("U", strtotime($response['mission']['end_date']));
    
$diff = round(($end_date - $start_date)/(60*60*24));

if ($diff>1) {
    $end_date = date("d.m", strtotime($response['mission']['end_date']));
    $start_date = date("d.m",  strtotime($response['mission']['start_date']));
    $text .="\nНа $diff дня , с $start_date";
}
        
$text = urlencode($text);        
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$text);

$sel = $mysqli->query("SELECT * FROM `mission` WHERE `start_date`<NOW() AND `end_date`>NOW()");

if ($sel->num_rows==0) {
    $query = "INSERT INTO `mission` (`start_date`,`end_date`,`type`,`condition`,`name`,`value`,`points`, `duration`, `mode`) 
                                 VALUES ('".$response['mission']['start_date']."',
                                         '".$response['mission']['end_date']."',
                                         '".$response['mission']['missionTemplate_id']['valueType']."',
                                         '".$response['mission']['missionTemplate_id']['condition']."',
                                         '".$response['mission']['name']."',
                                          ".$response['mission']['value'].",
                                          ".$response['mission']['reward'].",
                                          ".$diff.",
                                         '".$response['mission']['missionTemplate_id']['mode']."')";
    echo $query;
    $ins = $mysqli->query($query);
}

if ($response['mission']['missionTemplate_id']['mode']=="solo") {

    $sel = $mysqli->query("SELECT * FROM `squad` WHERE `jwt`<>'0'");

    for ($i=0; $i<$sel->num_rows; $i++){
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        $jwt = $res['jwt'];
            
        $squad_url ='https://api.squadrunner.co/api/v3/missions/subscribe/';

        $payload = array(
                    "user_id" => $res['squad_id'], 
                    "mission_id" => $mission_id
            ); 

        include($path.'squad/request.php');

        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$res['tg_id'].'&text=Принял миссию.');
    }
}

?>