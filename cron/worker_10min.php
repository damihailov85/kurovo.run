<?php

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');

$text = '';
$to_notice = [];


include($path.'cron/task/run_info_delete.php'); // зачистка начавшихся пробежек

include($path.'cron/task/boost_delete.php');  // зачистка сгоревших бустов

include($path.'cron/task/boost_count.php');  // перепись бустов

include($path.'cron/task/autoboost.php');

include($path.'cron/task/boost_count.php');  // перепись бустов, обновление после автовыдачи

include($path.'cron/task/run_notice.php'); // уведомление о предстоящих пробежках

$text = urlencode($text);
if ($text != '')
    file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$group_id.'&text='.$text);

include($path.'cron/task/token_delete.php');

exit('ok');
?>