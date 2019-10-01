<?php

$squad_url = 'https://api.squadrunner.co/api/v3/squad/squad/withmembers/';
$payload = array("squad_id" => $team_squad_id); 

include('/home/tfbddmzi/public_html/kurovo/squad/request.php');
$runners_list = $response;

$team_name = $runners_list['squad']['name'];
$team_id = $runners_list['squad']['_id'];

for ($j = 0; $j < 20; $j++) {
                
    $time = microtime(true) - $start;

    if ($time > 60) 
        break;
        
    $runner_first_name = $runners_list['members'][$j]['first_name'];
    $runner_last_name = $runners_list['members'][$j]['last_name'];
    $runner_id = $runners_list['members'][$j]['_id'];

    for ($k = 0; $k < 6; $k++) {
  
        echo "<br/>".$k."<br/>";
  
        $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';

        $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                         "offset" => $k, 
                         "sort" => "start_time", 
                         "sortAsc" => false, 
                         "user_id" => $runner_id); 

        include('/home/tfbddmzi/public_html/kurovo/squad/request.php');
                    
        if (isset($response['err'])) 
            break;
                    
        $activity_num = 0;
                    
        while(isset($response[$activity_num])){
                        
            $distance = 0;
            $duration = 0;
            $boost = 0;
            $activity = $response[$activity_num]['activity_type'];   

            $value = $response[$activity_num]['points'];
            $date = date("Y-m-d H:i:s", strtotime($response[$activity_num]['start_time']));
 
            if ($activity == "running") {
                $distance = round($response[$activity_num]['distance']/1000, 2);
                $duration = $response[$activity_num]['duration'];
                $boost = $response[$activity_num]['profiles'][0]['nb_boosts'];
                                
                if ($distance == 0) 
                    continue;
                }
                
                $query = "SELECT * FROM `activity` WHERE `squad_id`='".$runner_id."' AND `activity`='".$activity."' AND `date`='".$date."'";
                $sel_activity = $mysqli->query($query);
                                            
                if ($sel_activity->num_rows==0) {
                    $query = "INSERT INTO `activity` (`team`, `team_id`, `squad_id`, `name`, `activity`, `distance`, `duration`, `boost`, `value`, `date`) 
                                            VALUES('".$team_name."', '".$team_id."', '".$runner_id."', '".$runner_first_name."', '".$activity."', ".$distance.", ".$duration.", ".$boost.", ".$value.", '".$date."')";
                    $ins = $mysqli->query($query);
    
                }

                $activity_num++;
   
            }
        }
    }
}



?> 