<?php

$sel_q = $mysqli->query("SELECT * FROM `quiz`");
$sel_q->data_seek(0);
$res_q = $sel_q->fetch_assoc();
$answer = 'Сегодняшний ответ '.$res_q['answer'].', '.$res_q['answer_text'];

if (date("Y-m-d", strtotime($res_q['quiz_date']))!=date("Y-m-d")) {
    include($path.'quiz/search_answer.php');
}

else {

    $no_answer = [];
    $sel = $mysqli->query("SELECT `squad`.`jwt`, `squad`.`squad_id`, `user`.`name`, `user`.`tg_id` 
                                FROM `squad` 
                                JOIN `user` 
                                    ON `squad`.`tg_id`=`user`.`tg_id`
                                WHERE 1");

    for ($i = 0; $i < $sel->num_rows; $i++){ 
        $ctrl = 0;
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';
        
        $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                                    "offset" => 0, 
                                    "sort" => "start_time", 
                                    "sortAsc" => false, 
                                    "user_id" => $res['squad_id']);
                                    
        $jwt = $jwt_to_curl;
        
        include($path.'squad/request.php');
            
        for ($j=0; $j<5; $j++) {
            echo $res['name'].'('.$response[$j]['quizz']['start_date'].')'.' - '.date("Y-m-d", strtotime($response[$j]['quizz']['start_date'])).'/'.date("Y-m-d", time()-3600)."<br/>";
            if ($response[$j]['activity_type']=='quizz'&&date("Y-m-d", strtotime($response[$j]['quizz']['start_date']))==date("Y-m-d", time()-3600)) {
                $ctrl = 1;
                echo $ctrl."<br/>";
                break;
            }
        }
           
        if ($ctrl == 0) {

            if ($res['jwt']!==0&&$res['jwt']!=='0'){
        
                $jwt = $res['jwt'];
                include($path.'quiz/quiz_answer.php');
                usleep(1000000*rand(1,10));
            }
            else {
                file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$res['tg_id'].'&text='.$answer);
                $no_answer[count($no_answer)] = $res['name'];
            }
        }
    
    }

    if (count($no_answer)>0){
        $text = "Сегодня не ходили отвечать на квиз:\n";
        for ($i=0; $i < count($no_answer); $i++) {
            echo $no_answer[$i].'<br/>';
            $text .= "<b>".$no_answer[$i]."</b>\n";
        }
    }
    else {
        $text = 'Сегодня ответили все. Все - молодцы!!!';
    }
    
    echo $text;
    $text = urlencode($text);

    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$text);
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=quiz_control.php finished');

    exit('ok');
}
?>
