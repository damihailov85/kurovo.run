<?php

$sel = $mysqli->query("SELECT `squad_id`, `name` FROM `squad` JOIN `user` ON `squad`.`tg_id`=`user`.`tg_id`");    

for ($i = 0; $i < $sel->num_rows; $i++) {

    $sel->data_seek($i);
    $res = $sel->fetch_assoc();
    
    echo "<br/>".$res['squad_id']."<br/>";
    for ($k = 0; $k < 4; $k++) {
  
        $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';

        $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                        "offset" => $k, 
                        "sort" => "start_time", 
                        "sortAsc" => false, 
                        "user_id" => $res['squad_id']); 

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
                                
                if ($distance == 0) {
                    $activity_num++;
                    continue;
                }
            }
                
            $query = "SELECT * FROM `activity` WHERE `squad_id`='".$res['squad_id']."' AND `activity`='".$activity."' AND `date`='".$date."'";
            $sel_activity = $mysqli->query($query);
                                            
            if ($sel_activity->num_rows==0) {
                $query = "INSERT INTO `activity` (`team`, `team_id`, `squad_id`, `name`, `activity`, `distance`, `duration`, `boost`, `value`, `date`) 
                                            VALUES('".$team_squad_name."', '".$team_squad_id."', '".$res['squad_id']."', '".$res['name']."', '".$activity."', ".$distance.", ".$duration.", ".$boost.", ".$value.", '".$date."')";
                $ins = $mysqli->query($query);
            }
    
                   
            $activity_num++;
        }
    }
}

?> 