<?

$sel_runners = $mysqli->query("SELECT `run`.*,`t`.* 
									FROM `run` 
									LEFT JOIN 
										(SELECT `user`.`name`, `squad`.`squad_id`, `user`.`tg_id` 
											FROM `squad` 
											JOIN `user` 
												ON `squad`.`tg_id`=`user`.`tg_id`) 
										AS `t` 
										ON `run`.`tg_id`=`t`.`tg_id` 
										ORDER BY `date`");

$text = "";
if ($sel_runners->num_rows == 0){
  	$text = 'Никто не собирается бежать..';
} 
else {
	$run_arr = [];
  	for ($i = 0; $i < $sel_runners->num_rows; $i++){
    	$sel_runners->data_seek($i);
    	$res_runners = $sel_runners->fetch_assoc();

		$runner_squad = $res_runners['squad_id'];
		$run_date = $res_runners['date'];
		$runner_name = $res_runners['name'];
		$run_distance = $res_runners['distance'];
		$run_description = $res_runners['description'];
		$link = 1;
		include('run/runner.php');
		
		if ($message=='?'&&$boost_start==30)
			continue;
		
		$text .= $txt;
		
	}
}

send_msg($text, $user_id);
exit('ok');



?>