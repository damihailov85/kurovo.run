<pre>
<?php

$answer='';
$answer_control = 0;
$sel = $mysqli->query("SELECT * FROM `squad` WHERE `squad_id`<>'0'");

for ($i = 0; $i < $sel->num_rows; $i++) {

    $sel->data_seek($i);
    $res = $sel->fetch_assoc();

    $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';

    $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                        "offset" => 0, 
                        "sort" => "start_time", 
                        "sortAsc" => false, 
                        "user_id" => $res['squad_id']); 

    include('squad/request.php');
    
    for ($j=0; $j<5; $j++) {
        
        if ($response[$j]['activity_type']=='quizz') {

            $good_answer = $response[$j]['quizz']['good_answer'];
            
            $num_answer = ($response[$j]['quizz']['answer'.$good_answer][0]['language']=='en') ? 0 : 1;
            $text_answer = $response[$j]['quizz']['answer'.$good_answer][$num_answer]['name'];
            
            $num_question = ($response[$j]['quizz']['question'][0]['language']=='en') ? 0 : 1;           
            $question = $response[$j]['quizz']['question'][$num_question]['name'];
            
            $start_date = date("Y-m-d", strtotime($response[$j]['quizz']['start_date'])+7200);
            echo $start_date;
            $date = date("Y-m-d");
        
            if ($date==$start_date) { 

                $text_answer = str_replace( "'" , "\'" , $text_answer);
                $question = str_replace( "'" , "\'" , $question);
                $db_query = "SELECT * FROM `quiz` WHERE `quiz_id`='".$response[$j]['quizz']['_id']."'";
                echo $db_query."<br/>";
                $sel2 = $mysqli->query($db_query);
                            
                if ($sel2->num_rows==0){
                    
                    $first_request = 1; // Чтобы только при первом запросе отправлял в группу.
                    $query = "UPDATE `quiz` SET `quiz_id`='".$response[$j]['quizz']['_id']."', `question`='".$question."', `answer`='".$good_answer."', `answer_text`='".$text_answer."', `quiz_date`='".$start_date."' WHERE id=1";
                    $upd = $mysqli->query($query); 
                    $answer_control = 1;
                }  
                break;
            }
        }   
    }
}

if ($answer_control == 0){

    $sel = $mysqli->query("SELECT * FROM `squad` WHERE `tg_id`='".$fake_id."'");

    $sel->data_seek(0);
    $res = $sel->fetch_assoc();
    
    $jwt = $res['jwt'];

    $squad_url = 'https://api.squadrunner.co/api/v3/quizz/quizzesrunner/';

    $payload = array(
                "company_id" => "5a1554b82c1350cbd9afbade",
                "user_id" => $res['squad_id']
            ); 

    include($path.'squad/request.php');  

    $quiz_id = $response[0]['_id'];

          
    $squad_url = 'https://api.squadrunner.co/api/v3/quizz/saverunnerquizz/';

    $answer = $response[0]['quizz']['good_answer'];

    $payload = array(
                "answer" => $answer,
                "quizz_id" => $quiz_id,
                "user_id" => $res['squad_id']
            ); 


    include($path.'squad/request.php');        
            
                        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$admin_id.'&text='.$file_contents);
                        
    // повторный поиск. Уже только по фэйковому.
    
    $squad_url = 'https://api.squadrunner.co/api/v3/runner/runs/';

    $payload = array("company_id" => "5a1554b82c1350cbd9afbade", 
                        "offset" => 0, 
                        "sort" => "start_time", 
                        "sortAsc" => false, 
                        "user_id" => $res['squad_id']); 

    include($path.'squad/request.php');
    
    for ($j=0; $j<5; $j++) {
        
        if ($response[$j]['activity_type']=='quizz') {
        
          //  send_msg($j, $chat_id);
            $good_answer = $response[$j]['quizz']['good_answer'];
            
            $num_answer = ($response[$j]['quizz']['answer'.$good_answer][0]['language']=='en') ? 0 : 1;
            $text_answer = $response[$j]['quizz']['answer'.$good_answer][$num_answer]['name'];
            
            $num_question = ($response[$j]['quizz']['question'][0]['language']=='en') ? 0 : 1;           
            $question = $response[$j]['quizz']['question'][$num_question]['name'];
            
            $start_date = date("Y-m-d", strtotime($response[$j]['quizz']['start_date'])+7200);
            echo $start_date;
            $date = date("Y-m-d");
        
            if ($date==$start_date) { 

                $text_answer = str_replace( "'" , "\'" , $text_answer);
                $question = str_replace( "'" , "\'" , $question);
                $db_query = "SELECT * FROM `quiz` WHERE `quiz_id`='".$response[$j]['quizz']['_id']."'";
                echo $db_query."<br/>";
                $sel2 = $mysqli->query($db_query);
                            
                if ($sel2->num_rows==0){
                    
                    $first_request = 1; // Чтобы только при первом запросе отправлял в группу.
                    $query = "UPDATE `quiz` SET `quiz_id`='".$response[$j]['quizz']['_id']."', `question`='".$question."', `answer`='".$good_answer."', `answer_text`='".$text_answer."', `quiz_date`='".$start_date."' WHERE id=1";
                    $upd = $mysqli->query($query); 
                    $answer_control = 1;
                }  
                break;
            }
        }   
    }
}
?>

</pre>