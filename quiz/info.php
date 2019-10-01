<?php

$sel = $mysqli->query("SELECT * FROM `quiz`");
$sel->data_seek(0);
$res = $sel->fetch_assoc();
  
if (date("Y-m-d", strtotime($res['quiz_date']))==date("Y-m-d")) {
    $answer = "\xE2\x9D\x93 ".$res['question']."\n\xE2\x9D\x97 <b>(".$res['answer']."</b>) ".$res['answer_text'];
}
else {
    $answer = 'Я пока не знаю ответ, но сейчас посмотрю. Это займет некоторое время...';
    send_msg($answer, $chat_id);

    include('quiz/search_answer.php');

    $sel = $mysqli->query("SELECT * FROM `quiz`");
    $sel->data_seek(0);
    $res = $sel->fetch_assoc();
          
    if (date("Y-m-d", strtotime($res['quiz_date']))==date("Y-m-d")) {
        $answer = "\xE2\x9D\x93 ".$res['question']."\n\xE2\x9D\x97 <b>(".$res['answer']."</b>) ".$res['answer_text'];
    }
    else {
        $answer = 'Я хз, пока что никто не ответил...';
    }
}

if ($chat_id==$group_id||$first_request==1){
    send_msg($answer, $group_id);
}

if ($chat_id!=$group_id){
    send_msg($answer, $chat_id);
}

exit('ok');

?>