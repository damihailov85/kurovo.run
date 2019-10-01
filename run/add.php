<?

$control = 0;

if ($message > 10001 && $message < 235999) {

    $t = $message;

    $hour = floor($t / 10000);
    $min = floor(($t - $hour*10000)/100);
    $distance = $t - $hour*10000 - $min*100;

    if ($min >= 0 && $min < 60 && $hour > 0 && $hour < 24 && $distance > 0) {
        $control = 1;
    }
    else {
        $text = "\xE2\x9D\x97Часы должны быть в интервале от 1 до 23\n\xE2\x9D\x97Минуты - от 01 до 59\n\xE2\x9D\x97Дистанция должна быть задана двумя знаками, с ведущим нулем при значении менее 10 км";
    }
}

else {

    $msg = explode(' ', $message);

    for ($i = 0; $i < count($msg); $i++) {
        if (strpos($msg[$i], ':')>0) {

            $time = explode('!', $msg[$i]);
            $time = explode(':', $time[1]);

            $hour = $time[0];
            $min = $time[1];

            $distance = $msg[$i+1];

            $comment = '';
            for ($j = $i + 2; $j < count($msg); $j++) {
                $comment .= $msg[$j].' ';
            }

            if ($min >= 0 && $hour >= 0 && $min < 60 && $hour < 24 && is_numeric($distance)) {
                $control = 1;
            }
            else {
                $text = "\xF0\x9F\x98\x95 Непонятно... Проверь, всё ли сделано правильно: \n-Время указано правильное и через двоеточие?\n-Дистанция отделена от времени и комментария одним пробелом?\n-Между ! и временем ничего нет?\n\nВ случае, если всё верно, попробуй записаться ещё раз, и, если получишь это сообщение ещё раз, сообщи об ошибке $admin_login";
            }

            break;
        }
    }
}

if ($control==1) {

    $i = (($hour==date("H")&&$min<date("i"))||$hour<date("H")) ? 1 : 0;

    $date_run = date('Y-m-d H:i:s', mktime($hour, $min, 0, date("m") , date("d")+$i, date("Y")));

    

    $notice_start = ($user_id==$chat_id) ? 0 : 1;
    $query = "INSERT INTO `run`( `tg_id`, `date`, `distance`, `description`, `notice_start`) 
                        VALUES ('".$user_id."', '".$date_run."', ".$distance.", '".$comment."', ".$notice_start.")";
    $ins = $mysqli->query($query);

    $text = "Записана пробежка:\n".date("d.m H:i", strtotime($date_run))."\nДистанция ".$distance." км";
    if (count($comment) > 0) $text .= "\n\"".$comment."\"";

    $query = "INSERT INTO `sync`( `tg_id`, `date`) 
                        VALUES ('".$user_id."', DATE_ADD('".$date_run."', INTERVAL ".($distance*6)." MINUTE))";
    $ins = $mysqli->query($query);
}

send_msg($text, $user_id);

?>