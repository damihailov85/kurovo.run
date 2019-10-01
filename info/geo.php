<?php

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');

$arr = explode("_", $message);
$text = "";
$add = "";

if (isset($arr[1])) {
    
    if (isset($arr[2])&&$arr[2]=="who") { 
      
        $sel = $mysqli->query("SELECT `state`, `country`, ROUND(SUM(`distance`), 1) AS `dist` 
                                    FROM `geo` 
                                    JOIN `activity` 
                                        ON `activity`.`activity_id`=`geo`.`run_id` 
                                    WHERE `country_code`='".$arr[1]."' 
                                    GROUP BY `state` 
                                    ORDER BY `dist` DESC");


        $sel2 = $mysqli->query("SELECT CONCAT( `name`, `state`) AS `c`, `name`, `city`, `county`, `state`, `village`, `town`, `country`, ROUND(SUM(`distance`), 1) AS `dist` 
                                    FROM (SELECT `squad_id`, `city`, `county`, `state`, `village`, `town`, `country`, `distance` 
                                            FROM `geo` 
                                            JOIN `activity` 
                                                ON `activity`.`activity_id`=`geo`.`run_id` 
                                            WHERE `country_code`='".$arr[1]."') AS `r`
                                    JOIN (SELECT `name`, `squad_id` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id`) AS `u` 
                                        ON `r`.`squad_id`=`u`.`squad_id`

                                    WHERE 1 
                                    GROUP BY `c`
                                    ");
                                    
        if ($sel->num_rows>0) {
            for ($i = 0; $i < $sel->num_rows; $i++) {
                $sel->data_seek($i);
                $res = $sel->fetch_assoc();
                
                $text .= "<b>".$res['state'].": ".$res['dist']." км </b>\n";
                $list = [];
                for ($j = 0; $j < $sel2->num_rows; $j++) {
                    $sel2->data_seek($j);
                    $res2 = $sel2->fetch_assoc();
                    
                    if ($res2['state']==$res['state']){
                        for ($k = 0; $k < count($list); $k++) {
                            if (isset($list[$k][$res2['name']])) {
                                $list[$k][$res2['name']] += $res2['dist'];
                                break;
                            }
                        }
                        
                        $list[count($list)] = array ($res2['name'] => $res2['dist']);
                        
                    }
                }
                
                for ($k = 0; $k < count($list); $k++) {
                    foreach ($list[$k] as $key => $value) {
                        $text .= "  ".$key." ".$value."\n";
                    }
                }
            }
        }
    }
    else {
        
        if ($arr[1]=='city'){
            $sel = $mysqli->query("SELECT `place`, ROUND(SUM(`distance`), 1) AS `dist` FROM `geo` JOIN `activity` ON `activity`.`activity_id`=`geo`.`run_id` WHERE 1 GROUP BY `place` ORDER BY `dist` DESC");

            if ($sel->num_rows>0) {
                for ($i = 0; $i < $sel->num_rows; $i++) {
                    $sel->data_seek($i);
                    $res = $sel->fetch_assoc();
                    $text .= $res['place'].": ".$res['dist']." км \n";
                }
            }            
        }
        else {
            $text .= "Подробнее /geo_".$arr[1]."_who \n";
            $sel = $mysqli->query("SELECT `state`, `country`, ROUND(SUM(`distance`), 1) AS `dist` FROM `geo` JOIN `activity` ON `activity`.`activity_id`=`geo`.`run_id` WHERE `country_code`='".$arr[1]."' GROUP BY `state` ORDER BY `dist` DESC");

            if ($sel->num_rows>0) {
                for ($i = 0; $i < $sel->num_rows; $i++) {
                    $sel->data_seek($i);
                    $res = $sel->fetch_assoc();
                    $text .= $res['state'].": ".$res['dist']." км \n";
                }
            }
        }
    }
}
else {
    // по странам
    $sel = $mysqli->query("SELECT `country`, `country_code`, COUNT(`distance`) AS `count`, ROUND(SUM(`distance`), 1) AS `dist` FROM `geo` JOIN `activity` ON `activity`.`activity_id`=`geo`.`run_id` GROUP BY `country` ORDER BY `dist` DESC");
    $counter = 0;
    if ($sel->num_rows>0) {
        for ($i = 0; $i < $sel->num_rows; $i++) {
            $sel->data_seek($i);
            $res = $sel->fetch_assoc();
            $counter += $res['count'];
            $text .= $res['country'].": ".$res['dist']."  /geo_".$res['country_code']."\n";
        }
    }
    if ($user_id == $admin_id) $add = "\n Записано: ".$counter;
}

$text .= $add;

$text = urlencode($text);

file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);

exit();

?>