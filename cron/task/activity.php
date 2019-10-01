<?php

include($path.'tool/config.php');

$start = microtime(true);

$squad_url = 'https://api.squadrunner.co/api/v3/squad/squad/withmembers/';

$squad_id = date("h")==10 ? "5c52a87d822ecb2037a86e46" : "5a8bca301454a5262d7d0905";

$payload = array("squad_id" => $squad_id); 

include($path.'squad/request.php');

$runners_list = $response;

foreach ($runners_list['members'] as $runner) {
                
    $runner_id = $runner['_id'];
 
    $control = 1;
    $k = 0;
    $stop = 0;

    while ($control > 0) {
    
        if ($stop > 3 || $k > 50 ) // $k > 50 - на всякий случай) 
            break;
            
        $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';

        $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                "offset" => $k, 
                "sort" => "start_time", 
                "sortAsc" => false, 
                "user_id" => $runner_id); 

        include($path.'squad/request.php');
                    
        if (isset($response['err'])||!isset($response[0])) {
            $control = 1;
            break;
        }

        

        foreach ($response as $activity){
                        
            $distance = 0;
            $duration = 0;
            $boost = 0;
            $activity_type = $activity['activity_type'];   

            $value = $activity['points'];
            $date = date("Y-m-d H:i:s", strtotime($activity['start_time']));
 
            if ($activity_type == "running") {
                $distance = round($activity['distance']/1000, 2);
                $duration = $activity['duration'];
                $boost = $activity['profiles'][0]['nb_boosts'];
                                
                if ($distance == 0) 
                    continue;
            }

            

            $query = "SELECT * FROM `activity` WHERE `activity_id`='".$activity['_id']."'";
            $sel_activity = $mysqli->query($query);
                                                
            
            if ($sel_activity->num_rows==0) {
                $query = "INSERT INTO `activity` (`activity_id`, `squad_id`, `activity`, `distance`, `duration`, `boost`, `value`, `date`) 
                                    VALUES('".$activity['_id']."', '".$runner_id."', '".$activity_type."', ".$distance.", ".$duration.", ".$boost.", ".$value.", '".$date."')";
                $ins = $mysqli->query($query);
                echo $query."<br/>";
            }
    
            else {
                $stop++; // останавливать после первого повтора не стоит, т.к. миссии записываются на конец дня и могут потеряться чуть более старые.
                if ($stop>3)
                    break;
            }
                       
        }
        
        $k++; // переходим на следующую страницу активностей.
    }   
}


function top_list($dist, $section) {
    
    global $mysqli, $admin_id, $token, $group_id;

    $text = "";
    $results = array();
    $control = array();
    $upd = 0;

    if ($section == 'Дальше всех:(/reslong)') {
        
        $sel = $mysqli->query("SELECT `activity`.`activity_id`, `activity`.`rate`, `j`.`s`, `j`.`n`, `activity`.`duration`, `activity`.`distance`, `activity`.`duration`/`activity`.`distance` AS `pace`, `activity`.`date` 
                                        FROM `activity` 
                                        JOIN (SELECT `user`.`tg_id` AS `t`, `user`.`name` AS `n`, `squad`.`squad_id` AS `s` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id`) AS `j` 
                                            ON `activity`.`squad_id`=`j`.`s` 
                                        WHERE `activity`.`activity`='running' 
                                            AND `activity`.`distance` > 50
                                        ORDER BY `distance` DESC");
    }

    else {
        
        $sel = $mysqli->query("SELECT `activity`.`activity_id`, `activity`.`rate`, `j`.`s`, `j`.`n`, `activity`.`duration`, `activity`.`distance`, `activity`.`duration`/`activity`.`distance` AS `pace`, `activity`.`date` 
                                    FROM `activity` 
                                    JOIN (SELECT `user`.`tg_id` AS `t`, `user`.`name` AS `n`, `squad`.`squad_id` AS `s` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id`) AS `j` 
                                        ON `activity`.`squad_id`=`j`.`s` 
                                    WHERE `activity`.`activity`='running' 
                                    AND `activity`.`distance` > ".($dist*0.9)." 
                                            AND `activity`.`distance` < ".($dist*1.1)." 
                                    ORDER BY `pace` ASC");                               
    }

    if ($sel->num_rows>0) {
        for ($i = 0; $i < $sel->num_rows; $i++) {
            $sel->data_seek($i);
            $res = $sel->fetch_assoc();
        
            if (!in_array($res['s'], $control)){ // проверяю, первая ли это строчка по этому бегуну.
            
                $new = 0;
                $control[count($control)] = $res['s'];
            
                if ($res['rate']!=count($control)) {
                    $upd = 1;                    
                }
                
                $new = ($res['rate'] > count($control)) ? 1 : 0;
                
                
                $pace = floor($res['pace']/60)."м".round($res['pace']%60)."c";
                $duration = floor($res['duration']/3600)."ч".floor($res['duration']%3600/60)."м".round($res['duration']%60)."c";
                
                $results[count($results)] = array("name" => $res['n'], 
                                            "squad_id" => $res['s'], 
                                            "activity_id" => $res['activity_id'], 
                                            "pace" => $pace, 
                                            "date" => $res['date'], 
                                            "duration" => $duration,
                                            "distance" => $res['distance'],
                                            "new" => $new
                                        );

                if (count($control)==3)
                    break;
            }
        }
    }
    
    if ($upd == 1) {
        
        if ($section == 'Дальше всех:(/reslong)') {
            $mysqli->query("UPDATE `activity` SET `rate` =4  WHERE `activity`.`distance` > 50"); 
        }
        else {
            $mysqli->query("UPDATE `activity` SET `rate` =4  WHERE `activity`.`distance` > ".($dist*0.9)." AND `activity`.`distance` < ".($dist*1.1)); 
        }
        
        $text .= "А у нас изменился список лидеров ";
        $text .= ($section == 'Дальше всех:(/reslong)') ? " в списке самых дальних забегов" : "на дистанции ".$dist." км";
        $text .= "!!!\n";

        $k = 1;
        foreach($results as $key => $res){
            $text .= ($res['new'] == 1) ? "<b>" : "";
            $text .= $res['name']." ".date("d.m.Y", strtotime($res['date']))."\n".$res['distance']."км за ".$res['duration']."(темп: ".$res['pace'].")\n";
            $text .= ($res['new'] == 1) ? "</b>" : "";
            
            $mysqli->query("UPDATE `activity` SET `rate` = ".$k." WHERE `activity_id` = '".$res['activity_id']."'"); 
            $k++;
        }

        $text .= "\n\n";
        $text = urlencode($text);
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$text);
    }
}

top_list(5, "Пятак(/res5)");

top_list(10, "Десятка(/res10)");

top_list(21.1, "Половинка(/res21)");

top_list(42.2, "Марафон(/res42)");

top_list("Самый длинный забег", "Дальше всех:(/reslong)");



?> 