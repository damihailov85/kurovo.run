<?php

$sel = $mysqli->query("SELECT * FROM `run` WHERE `tg_id`=".$user_id);
    
if ($sel->num_rows==0){
    $text = urlencode("Чтобы удалить что-нибудь ненужное, надо сначала записать что-нибудь ненужное!");
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text='.$text);
}
else {
    $del = $mysqli->query("DELETE FROM `run` WHERE `tg_id`=".$user_id); 
    $text = "Все пробежки удалены!";
    send_msg($text, $user_id);
}

?>