<?php

$list = '';
$sel = $mysqli->query("SELECT `user`.`tg_id`, `user`.`name`, `squad`.`jwt`, `squad`.`jwt_date`, DATEDIFF(NOW(),`squad`.`jwt_date`) AS `date_diff` , `squad`.`url`, `squad`.`payload` FROM `squad` JOIN `user` ON `squad`.`tg_id`=`user`.`tg_id` WHERE `squad`.`jwt`<>'0'");

if ($sel->num_rows > 0){
    for ($i=0; $i<$sel->num_rows; $i++){
        
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        $user_id = $res['tg_id'];

        $q = explode(".", $res['jwt']);
        $decoded_token_part = base64_decode($q[1]); 
        $qwe = json_decode($decoded_token_part, true);
            
        if ($qwe['exp']) {
            $days_to_end = floor(($qwe['exp'] - time())/(60 * 60 * 24));
            $list .= $res['name'];
            if ($res['url']!='0'&&$res['payload']!='0') {
                $list .= "\xF0\x9F\x94\x91";
            }
            else {
               
                if($days_to_end<4)
                    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text=Нужно обновить токен до '.$end_date);
            }
            $list .= "\n";
                
            $hours_to_end = floor(($qwe['exp'] - time())/(60 * 60));
            $list .= "\xF0\x9F\x93\x86 ".$days_to_end." \xF0\x9F\x95\x97 ".$hours_to_end." \n(".date("d.m H:i", $qwe['exp']).")\n\n";
            $end_date = date("d.m H:i", $qwe['exp']);
        }
    
        else {
            $days_to_end = 30 - $res['date_diff'];
            $list .= $res['name'];
            if ($res['url']!='0'&&$res['payload']!='0') {
                $list .= "\xF0\x9F\x94\x91";
            }
            else {
                
                if($days_to_end<4)
                    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text=Нужно обновить токен до '.$end_date);
            }
            $list .= "\n";
            $list .= "\xF0\x9F\x93\x86 ".$days_to_end."\n(".$res['jwt_date'].")\n\n";
            $end_date = $res['jwt_date'];
        }
/*
\xE2\x9C\x94 галочка 
\xE2\x9C\x96 крестик
\xF0\x9F\x93\x86 календарь
\xF0\x9F\x95\x97 часы
\xF0\x9F\x94\x92 закрытый замок
\xF0\x9F\x94\x91 ключ 
\xF0\x9F\x94\x93 открытый замок
*/
    }
    $list = urlencode($list);
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text='.$list);
}



?>