<pre>
<?php

$text = "";

function top_list($dist, $section) {
    
    global $mysqli, $text;

    $results = array();
    $control = array();

    $text .= $section.":\n";

    if ($section == 'Дальше всех:(/reslong)') {
        
        $sel = $mysqli->query("SELECT `activity`.`activity_id`, `j`.`s`, `j`.`n`, `activity`.`duration`, `activity`.`distance`, `activity`.`duration`/`activity`.`distance` AS `pace`, `activity`.`date` 
                                        FROM `activity` 
                                        JOIN (SELECT `user`.`tg_id` AS `t`, `user`.`name` AS `n`, `squad`.`squad_id` AS `s` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id`) AS `j` 
                                            ON `activity`.`squad_id`=`j`.`s` 
                                        WHERE `activity`.`activity`='running' 
                                        ORDER BY `distance` DESC");
    }

    else {
        
        $sel = $mysqli->query("SELECT `activity`.`activity_id`, `j`.`s`, `j`.`n`, `activity`.`duration`, `activity`.`distance`, `activity`.`duration`/`activity`.`distance` AS `pace`, `activity`.`date` 
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
        
            if (!in_array($res['s'], $control)){ 
            
                $control[count($control)] = $res['s'];
            
                $pace = floor($res['pace']/60)."м".round($res['pace']%60)."c";
                $duration = floor($res['duration']/3600)."ч".floor($res['duration']%3600/60)."м".round($res['duration']%60)."c";
        
                $text .= "<b>".$res['n']."</b> ".date("d.m.Y", strtotime($res['date']))."\n".$res['distance']."км за ".$duration."(темп: ".$pace.")\n";
                
                if (count($control)==3)
                    break;
            }
        }
    }

    $text .= "\n\n";
}

top_list(5, "Пятак(/res5)");

top_list(10, "Десятка(/res10)");

top_list(21.1, "Половинка(/res21)");

top_list(42.2, "Марафон(/res42)");

top_list(0, "Дальше всех:(/reslong)");

$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);


?>

</pre>