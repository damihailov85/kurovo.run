<?php 

$sel = $mysqli->query("SELECT `squad_id`, COUNT(`distance`) AS `count`
                                FROM `activity` 
                                WHERE `activity`='running' AND
                                    WEEK(`date` - INTERVAL 1 DAY)=WEEK(NOW() - INTERVAL 1 DAY) AND 
                                    `squad_id` IN (SELECT `squad_id` FROM `squad` WHERE `team`=1) AND
                                     YEAR(`activity`.`date`) = YEAR(NOW())
                                GROUP BY `squad_id`");

$beer = 60;

if ($sel->num_rows>0) {
    for ($i = 0; $i < $sel->num_rows; $i++) {
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();
        $beer -= ($res['count']>3) ? 3 : $res['count'];
    }

    $text = "До пива осталось ". $beer . " пробежек\n";

    $k = 0;
    for ($i = 0; $i < 6; $i++) {
        for ($j = 0; $j < 10; $j++) {
            $text .= ($k < 60 - $beer) ? "\xF0\x9F\x8D\xBA" : "\xF0\x9F\x90\x8E";
            $k++;
        }
        $text .= "\n";
    }

    if (in_array($user_id, $admin_list)&&$chat_id==$user_id) {
        $text .= "/detail";
    }
}

else {
    $text = "На этой неделе ещё никто не побежал за пивом...";
}

$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);

?>