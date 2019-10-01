<?php

$token = '*******';
$bot_name = "@*******";

$group_id = '*******';
$admin_id = '*******';
$admin_login = '@*******';
$admin_list = ['*******', '*******', '*******'];

$main_page = '*******';	
$path = '*******';

$mysqli = new mysqli("*******", "*******", "*******", "*******");

if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Не удалось подключиться к MySQL на C-Host');
    exit();
}

$time_zone = 'Europe/Moscow';
date_default_timezone_set($time_zone);
$set = $mysqli->query("SET time_zone = '+03:00'");

$team_squad_id = '*******';
$team_squad_name = '*******';

$sel = $mysqli->query("SELECT * FROM `squad` WHERE `jwt`<>'0'");
$i = rand(0, $sel->num_rows-1);
$sel->data_seek($i);
$res = $sel->fetch_assoc();
$jwt = $res['jwt'];
$jwt_to_curl = $res['jwt'];
$squad_id_to_curl = $res['squad_id'];

$fake_id = '*******';

if (!$mysqli->set_charset("utf8mb4")) { 
    printf("Ошибка при загрузке набора символов utf8: %s\n", $mysqli->error); 
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$admin_id.'&text=Ошибка при загрузке набора символов utf8');
    exit(); 
}

?>