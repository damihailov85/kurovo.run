<?php

$sel = $mysqli->query("SELECT `msg`.`m` AS `m`, `r`.`d` AS `d`, ROUND(`msg`.`m`/`r`.`d`, 1) AS `q`, `msg`.`n` AS `name`, `r`.`squad_id` 
                        	FROM (SELECT COUNT(`log`.`message`) AS `m`, `name` AS `n`, `user`.`tg_id` AS `tg_id` 
          		                        FROM `log` 
          		                        JOIN `user` 
          			                        ON `log`.`tg_id`=`user`.`tg_id` 
          		                        WHERE MONTH(`log`.`time`)=MONTH(NOW()) AND 
          			                        YEAR(`log`.`time`)=YEAR(NOW()) AND 
          			                        `log`.`chat_id`='-2147483648' 
          		                        GROUP BY `user`.`tg_id` ) AS `msg` 
	                        JOIN (SELECT SUM(`distance`) AS `d`, `tg_id`, `squad`.`squad_id` AS `squad_id`
          		                        FROM `activity` 
          		                        JOIN `squad` 
          			                        ON `activity`.`squad_id`=`squad`.`squad_id`
          		                        WHERE MONTH(`activity`.`date`)=MONTH(NOW()) AND 
          			                        YEAR(`activity`.`date`)=YEAR(NOW()) 
         		                        GROUP BY `squad`.`tg_id`) AS `r`
		                        ON `msg`.`tg_id`=`r`.`tg_id`  
                            ORDER BY `q`  DESC");

if ($sel->num_rows>0) {
    $text = '';
    for($i=0; $i<$sel->num_rows; $i++){
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        if ($res['m'] > 0 && $res['d'] > 0)
        $text .= ($i+1).". ".$res['name']." - ".$res['q']."\n";
    }

}
else {
    $text = "Рановато для таких оценок..";
}
send_msg($text, $chat_id);

?>