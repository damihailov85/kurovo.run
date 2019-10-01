<pre>
<?php

$sel2 = $mysqli->query("SELECT * FROM `quiz`");
$sel2->data_seek(0);
$res2 = $sel2->fetch_assoc();

if (date("Y-m-d", strtotime($res2['quiz_date']))==date("Y-m-d")) {

    $squad_url = 'https://api.squadrunner.co/api/v3/quizz/saverunnerquizz/';

    $payload = array(
                "answer" => $res2['answer'],
                "quizz_id" => $res2['quiz_id'],
                "user_id" => $res['squad_id']
            ); 


    include($path.'squad/request.php');
            

    if ($response['good_answer']==true){
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$res['tg_id'].'&text=Правильный ответ отправлен');
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$admin_id.'&text='.$res['name'].'___'.$response['good_answer']);
    }

    if ($response['good_answer']==false){
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$res['tg_id'].'&text='.$response['good_answer']);       
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$res['tg_id'].'&text=Кажется, отправился неверный ответ.. ');       
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$admin_id.'&text='.$res['name'].'___'.$file_contents);
    }
}
else {
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$res['tg_id'].'&text=Ещё нет ответа...');
}


?>

</pre>