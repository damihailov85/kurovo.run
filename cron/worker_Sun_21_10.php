<?php

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');

$chat_id = $admin_id;

include($path.'cron/task/review_week.php');

include($path.'cron/info/beer.php');

include($path.'cron/info/challenge.php');


exit('ok');

?>