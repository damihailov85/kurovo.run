<?php

$list = '';
$sel = $mysqli->query("SELECT `user`.`tg_id`, 
                                `user`.`name`, 
                                `squad`.`jwt`, 
                                `squad`.`jwt_date`, 
                                DATEDIFF(NOW(), `squad`.`jwt_date`) AS `date_diff` , 
                                `squad`.`url`, 
                                `squad`.`payload` 
                        FROM `squad` 
                        JOIN `user` 
                            ON `squad`.`tg_id`=`user`.`tg_id` 
                        WHERE `squad`.`jwt`<>'0'");

if ($sel->num_rows > 0){
    for ($i=0; $i<$sel->num_rows; $i++){
        $del = 0;
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        $user_id = $res['tg_id'];

        $q = explode(".", $res['jwt']);
        $decoded_token_part = base64_decode($q[1]); 
        $qwe = json_decode($decoded_token_part, true);
            
        if ($qwe['exp']) {
            $days_to_end = floor(($qwe['exp'] - time())/(60 * 60 * 24));
            $hours_to_end = floor(($qwe['exp'] - time())/(60 * 60));
            if ($res['url']!='0'&&$res['payload']!='0'&&$res['url']!=0&&$res['payload']!=0) {
                if ($hours_to_end < rand(5,50)) {
                    $del = 1;
                }
            }
            else {
                if ($hours_to_end < 1) {
                    $del = 1;
                }
            }
        }
        else {
            $days_to_end = 30 - $res['date_diff'];
            if ($days_to_end < 1) {
                $del = 1;
            }
        }

        if($del == 1) {
            $query = "UPDATE `squad` SET `jwt`=0, `auto`=0 WHERE `tg_id`=".$user_id;
            $upd = $mysqli->query($query);
            echo $query."<br/>";
            file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text=Токен удален');
            file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Удален токен: '.$res['name']);
            if ($res['url']!='0'&&$res['payload']!='0') {
                echo "Есть данные для авторизации<br/>".$res['url']."<br/>".$res['payload']."<br/>";
                include($path.'tool/connect.php');   
            }
        }
    }
}

?>