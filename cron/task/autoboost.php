<?php

$boost = [];
$run = [];
$result = [];

$sel_run = $mysqli->query("SELECT `squad`.`squad_id` AS `id`,  
                                    COUNT(`boost`.`boost_death_time`) AS `boost_now`, 
                                    COUNT(CASE WHEN `boost`.`boost_death_time`>`r`.`run_time` THEN 1 ELSE NULL END) AS `boost_start`,
                                    `r`.`run_time`, 
                                    `boost`.`boost_death_time` AS `boost_time`, 
                                    `r`.`dist` 
                                FROM `squad`
                                JOIN  (SELECT `tg_id`, SUM(`distance`) AS `dist`, MIN(`date`) AS `run_time` FROM `run` WHERE 1 GROUP BY `tg_id`) AS `r` 
                                    ON `squad`.`tg_id`=`r`.`tg_id` 
                                LEFT JOIN `boost` 
                                    ON `boost`.`recipient_squad_id`=`squad`.`squad_id` 
                                WHERE `squad`.`team` = 1 AND 
                                	(`dist` >= 10 OR `run_time` < NOW() + INTERVAL 1 HOUR )
                                GROUP BY `squad`.`squad_id`
                                ORDER BY `dist` DESC");

if ($sel_run->num_rows>0) {
    for ($j = 0; $j < $sel_run->num_rows; $j++) {
        $sel_run->data_seek($j);
        $res_run = $sel_run->fetch_assoc();

        $run[$j] = array('id'=>$res_run['id'], 
                            'time' => $res_run['run_time'], 
                            'boost' => 0, 
                            'boost_now' => $res_run['boost_now'], 
                            'boost_start' => $res_run['boost_start'], 
                            'distance' => $res_run['dist']);
    }
} 

$sel_boost = $mysqli->query("SELECT * FROM `squad` WHERE `squad_id` NOT IN (SELECT `sender_squad_id` FROM `boost`) AND 
                                                        `squad_id`<>0 AND 
                                                        `team`=1 AND `jwt`<>'0'");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        
        $boost[count($boost)] = array('id'=>$res_boost['squad_id'], 
                                        'time' => 0, 
                                        'send_to' => 0);
    }
}

$sel_boost = $mysqli->query("SELECT * FROM `squad` WHERE `squad_id` NOT IN (SELECT `sender_squad_id` FROM `boost`) AND 
                                                        `squad_id`<>0 AND 
                                                        `team`=1 AND `jwt`='0'");

if ($sel_boost->num_rows>0) {
    for ($i = 0; $i < $sel_boost->num_rows; $i++) {
        $sel_boost->data_seek($i);
        $res_boost = $sel_boost->fetch_assoc();
        $boost[count($boost)] = array('id'=>$res_boost['squad_id'], 
                                        'time' => 0, 
                                        'send_to' => 0);
    }
}

for ($i = 0; $i < count($boost); $i++) {
    for($j=0; $j<count($run); $j++) {
        if ($run[$j]['time'] > $boost[$i]['time'] && $run[$j]['boost_now'] + $run[$j]['boost'] < 3 && $boost[$i]['send_to'] == 0 && $boost[$i]['sender_squad_id'] != $run[$j]['id'] && $boost[$i]['squad_id'] != $run[$j]['id']) {
            $boost[$i]['send_to'] = $run[$j]['id'];
            $run[$j]['boost'] += 1;
        }
    }
}

for ($i = 0; $i < count($boost); $i++) {
    if ($boost[$i]['time'] == 0 && $boost[$i]['send_to'] != 0) {
        $sel = $mysqli->query("SELECT `name`, `jwt`, `user`.`tg_id`  FROM `squad` JOIN `user` ON `squad`.`tg_id`=`user`.`tg_id` WHERE `squad_id` = '".$boost[$i]['id']."'");
        
        if ($sel->num_rows>0) {
            $sel->data_seek(0);
            $res = $sel->fetch_assoc();

            $sel_name = $mysqli->query("SELECT `user`.`name` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id` 
                                                        WHERE `squad`.`squad_id`='".$boost[$i]['send_to']."'");
            $sel_name->data_seek(0);
            $res_name = $sel_name->fetch_assoc();
            
            if ($res['jwt']<>'0') {
                $squad_url = 'https://api.squadrunner.co/api/v3/enhancements/add/';
                $jwt = $res['jwt'];
                $from_user_id =  $boost[$i]['id'];
                $to_user_id = $boost[$i]['send_to'];
                $payload = array( "company_id" => "5a1554b82c1350cbd9afbade",
                                "enhancement_id" => "5a1562972c1350cbd9cd85bb",
                                "from_user_id" => $from_user_id,
                                "to_user_id" => $to_user_id ); 

                $sel_name = $mysqli->query("SELECT `user`.`name` FROM `user` JOIN `squad` ON `user`.`tg_id`=`squad`.`tg_id` 
                                                                WHERE `squad`.`squad_id`='".$boost[$i]['send_to']."'");
                $sel_name->data_seek(0);
                $res_name = $sel_name->fetch_assoc();

                if ($from_user_id !== $to_user_id) {
                    include($path.'squad/request.php');
                    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Автобуст '.$res['name'].' -> '. $res_name['name']);
                    
                    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$res['tg_id'].'&text=Твой буст улетел. Получатель: '.$res_name['name']);
                    
                    $d = date('Y-m-d H:i:s');
                }

            }
            else {
                $sel2 = $mysqli->query("SELECT `user`.`name`, `squad`.`squad_id` FROM `squad` JOIN `user` ON `squad`.`tg_id` = `user`.`tg_id` WHERE `squad_id` = '".$boost[$i]['send_to']."'");
                if ($sel2->num_rows>0) {
                    $sel2->data_seek(0);
                    $res2 = $sel2->fetch_assoc();
                    
                    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$res['tg_id'].'&text=Будет круто, если '.$res_name['name'].' получит твой буст..');
                }
            }
        }
    }
}

?>