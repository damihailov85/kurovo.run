<?php

$ins = $mysqli->query("INSERT INTO `log` (`tg_id`, `chat_id`, `message`) VALUES (".$user_id.", ".$chat_id.", '".$message."')");

if (($message[0]=='!'&&strlen($message)>5)||($message > 10001 && $message < 235999)){
    include('run/add.php');
    exit('ok');
}

if (substr($message, 0, 3)=='JWT'&&$user_id==$chat_id){
    include('squad/token.php');
    exit('ok');
}

if (substr($message, 0, 6)=='/stat_'){
    include('info/stat.php');
    exit('ok');
}

if (substr($message, 0, 4)=='/geo'){
    include('info/geo.php');
    exit('ok');
}

switch ($message) {
    
    case '/beer':
    case '/beer'.$bot_name:
        include('info/beer.php');
        break;
        
    case '/detail':
    case '/detail'.$bot_name:
        include('info/beer_detail.php');
        break;
        
    case '/team':
    case '/team'.$bot_name:
        include('info/beer_detail.php');
        break;

    case '/boost_list':
    case '/boost_list'.$bot_name:
        include('boost/list.php');
        break;

    case '/challenge':
    case '/challenge'.$bot_name:
        include('info/challenge.php');
        break;
       
    case '?':
    case '??':
    case '/who_run'.$bot_name:
    case '/who_run':
        include('run/list.php');
        break;

    case '/del_run':
    case '/del_run'.$bot_name:
        include('run/del.php');
        break;
    
    case '/my_boost':
    case '/my_boost'.$bot_name:
        include('boost/personal.php');
        break;

    case '/results':
    case '/results'.$bot_name:
        include('info/results.php');
        break;

    case '/res5':
    case '/res5'.$bot_name:
        include('info/res5.php');
        break;

    case '/res10':
    case '/res10'.$bot_name:
        include('info/res10.php');
        break;

    case '/res21':
    case '/res21'.$bot_name:
        include('info/res21.php');
        break;

    case '/res42':
    case '/res42'.$bot_name:
        include('info/res42.php');
        break;

    case '/reslong':
    case '/reslong'.$bot_name:
        include('info/reslong.php');
        break;

    case '/quiz':
    case '/quiz'.$bot_name:
        include('quiz/info.php');
        break;

    case '/msg_today':
    case '/msg_today'.$bot_name:
        include('info/msg_today.php');
        break;
    
    case '/msg_month':
    case '/msg_month'.$bot_name:
        include('info/msg_month.php');
        break;
        
    case '/msg_km':
    case '/msg_km'.$bot_name:
        include('info/msg_km_month.php');
        break;

    case '/week_mission':
    case '/week_mission'.$bot_name:
        include('mission/week.php');
        break;
    
    case '/stat':
    case '/stat'.$bot_name:
        include('info/stat_list.php');
        break;

    case '/sync':
    case '/sync'.$bot_name:
        include('squad/sync.php');
        break;
        
    case '/autoboost':
    case '/ab':
        include('cron/task/autoboost.php');
        break;

    case '/squad':
    case '/s':
    case '/squad'.$bot_name:
    case '/s'.$bot_name:
        include($path.'cron/task/boost_count.php');
        include($path.'cron/task/autoboost.php');
        break;
        
    case '/update_activity':
    case '/ua':
    case '/update_activity'.$bot_name:
    case '/ua'.$bot_name:
        include($path.'cron/task/activity.php');
        break;
}

if (in_array($user_id, $admin_list)) {
    switch ($message) {

        case '/quiz_control':
        case '/qc':
            include('quiz/control.php');
            break;
}

exit('ok');














}

?>