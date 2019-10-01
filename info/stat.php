<?php

$arr = explode("_", $message);
$text = "";

switch ($arr[1]."_".$arr[2]) {
    
    case 'c_y':
        $where = " AND YEAR(`date`)=YEAR(NOW())";
        break;
    case 'p_y':
        $where = " AND YEAR(`date`)=YEAR(NOW())-1";
        break;
    case 'c_m':
        $where = " AND YEAR(`date`)=YEAR(NOW()) AND MONTH(`date`)=MONTH(NOW())";
        break;
    case 'p_m':
        $where = " AND ((YEAR(`date`)=YEAR(NOW()) AND MONTH(`date`)=MONTH(NOW())-1) OR (YEAR(`date`)=YEAR(NOW())-1 AND MONTH(`date`)=MONTH(NOW())+11))";
        break;
}


if ($arr[3]=="d") $order_by = "ORDER BY SUM(`distance`) DESC";
if ($arr[3]=="t") $order_by = "ORDER BY SUM(`duration`) DESC";
if ($arr[3]=="p") $order_by = "ORDER BY ROUND(SUM(`duration`)/SUM(`distance`))";
if ($arr[3]=="c") $order_by = "ORDER BY COUNT(`distance`) DESC";


$sel = $mysqli->query("SELECT `j`.`n`, COUNT(`activity`.`distance`) AS `count_distance`, ROUND(SUM(`distance`), 1) AS `sum_distance`, SUM(`duration`) AS `sum_duration`, ROUND(SUM(`duration`)/SUM(`distance`)) AS `pace`
                                        FROM `activity` 
                                        JOIN (SELECT `user`.`tg_id` AS `t`, `user`.`name` AS `n`, `squad`.`squad_id` AS `s` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id`) AS `j` 
                                            ON `activity`.`squad_id`=`j`.`s` 
                                        WHERE `activity`.`activity`='running' ".$where."
                                        GROUP BY `j`.`s`  
                                        ".$order_by);
 

if ($sel->num_rows>0) {
    for ($i = 0; $i < $sel->num_rows; $i++) {
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        
        $pace = floor($res['pace']/60).":";
        $pace .= (round($res['pace']%60)<10) ? "0" : "";
        $pace .= round($res['pace']%60);

        $duration = floor($res['sum_duration']/3600).":";
        $duration .= (floor($res['sum_duration']%3600/60)<10) ? "0" : "";
        $duration .= floor($res['sum_duration']%3600/60).":";
        $duration .= (round($res['sum_duration']%60)<10) ? "0" : "";
        $duration .= round($res['sum_duration']%60);

        switch ($arr[3]) {
    
            case 'd':
                $text .= "<b>".$res['sum_distance']."</b> ".$res['n']."\n";
                break;
            case 't':
                $text .= "<b>".$duration."</b> ".$res['n']."\n";
                break;
            case 'p':
                $text .= "<b>".$pace."</b> ".$res['n']."\n";
                break;
            case 'c':   
                $text .= "<b>".$res['count_distance']."</b> ".$res['n']."\n";
                break;
        }
    }
}

$text .= "/stat";
$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);

exit();

?>