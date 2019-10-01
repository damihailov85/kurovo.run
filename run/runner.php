<?php

//на входе
//$runner_squad
//$run_date 
//$runner_name 
//$run_distance
//$run_description
//$link = 1/0 нужно ли добавлять ссылку

//смотрим текущее кол-во бустов
$sel_boost = $mysqli->query("SELECT * FROM `boost` WHERE `recipient_squad_id`='".$runner_squad."'");
$boost_now = ($sel_boost->num_rows==0) ? 0 : $sel_boost->num_rows*10;

//смотрим, сколько будет на момент старта
$sel_boost = $mysqli->query("SELECT * FROM `boost` WHERE `recipient_squad_id`='".$runner_squad."' AND `boost_death_time`>'".$run_date."'");
$boost_start = ($sel_boost->num_rows==0) ? 0 : $sel_boost->num_rows*10;

$boost = "\xE2\x9A\xA1 ".$boost_now."%; будет: <b>".$boost_start."%</b>";


$time = date("H:i", strtotime($run_date));
$time .= date("d", strtotime($run_date))!=date("d") ? "(завтра)" : "";
    

$name = ($link==1) ? "<a href='https://squadrunner.co/app/en/app/runner/".$runner_squad."'>".$runner_name."</a>" : "<b>".$runner_name."</b>";


$distance = ($run_distance>0) ? ", <b>".$run_distance." км</b>" : "";
    
if ($run_description != '') 
    $run_description .= "\n";

$txt = $name."\n".$time.$distance."\n".$boost."\n".$run_description."______________________\n";

?>