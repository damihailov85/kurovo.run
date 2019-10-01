<?php

if ($arr['message']['text']) {

    $sel = $mysqli->query("SELECT * FROM `user` WHERE `tg_id`='".$user_id."'");
    
    if (!$sel->num_rows) {

        if (isset($arr['message']['from']['username'])) $un = $arr['message']['from']['username']; else $un = '';
        $ins = $mysqli->query("INSERT INTO `user`( `tg_id`, `username`, `name`) 
                                VALUES ('".$user_id."', '".$un."','".$name."')");

        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text=Ты зарегистрирован(а), '.$name.'!');
    }

    else {
        $sel->data_seek(0);
        $res = $sel->fetch_assoc();
        if (isset($arr['message']['from']['username']))
        if ($arr['message']['from']['username']!=$res['username']) 
            $upd = $mysqli->query("UPDATE `user` SET `username`='".$arr['message']['from']['username']."' WHERE `tg_id`=".$user_id);
        if ($name!=$res['name'])
            $upd = $mysqli->query("UPDATE `user` SET `name`='".$name."' WHERE `tg_id`=".$user_id);
        
    }
}

$sel_squad = $mysqli->query("SELECT * FROM `squad` WHERE `tg_id`='".$user_id."'");
if ($sel_squad->num_rows > 0) {

    $sel_squad->data_seek(0);
    $res_squad = $sel_squad->fetch_assoc();
    $squad_id = $res_squad['squad_id'];

    $my_jwt = ($res_squad['jwt'] <> '0') ? $res_squad['jwt'] : '0';

}


?>