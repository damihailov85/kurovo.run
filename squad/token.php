<?php

$upd = $mysqli->query("UPDATE `squad` SET `auto`=1, `jwt`='".$message."', `jwt_date`='".date("Y-m-d")."' WHERE `tg_id`=".$user_id);
$jwt = explode(" ", $message);
$jwt = explode("\"exp\":", base64_decode($jwt[1]));
$jwt = explode(",", $jwt[1]);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$user_id.'&text=Записано. Токен действует до '.date("d.m H:i", $jwt[0]).'. Постараюсь напомнить ближе к завершению))');

?>