<?php

$sel=$mysqli->query("SELECT SUM(distance) AS `sum` FROM `activity` WHERE 
                                                                        DAY(`date`)<=DAY(NOW()) AND 
                                                                        (
                                                                            (MONTH(`date`)=MONTH(NOW())-1 AND YEAR(`date`)=YEAR(NOW())) 
                                                                                OR 
                                                                            (MONTH(`date`)=MONTH(NOW())+11 AND YEAR(`date`)=YEAR(NOW())-1)  
                                                                        )
                                                                        ");
$sel->data_seek(0);
$res=$sel->fetch_assoc();
$sum_previous_month = $res['sum'];


$sel=$mysqli->query("SELECT SUM(`distance`) AS `sum` FROM `activity` WHERE MONTH(`date`)=MONTH(NOW()) AND 
                                                                            YEAR(`date`)=YEAR(NOW())");
$sel->data_seek(0);
$res=$sel->fetch_assoc();
$sum = $res['sum'];

$text = "Текущий месяц:\n"; 

$text .= "\xE2\x98\x83 ".round($sum, 1)." км";
if ($user_id==519345226) $text .= " / ".round($sum/1.609344, 1)." миль";
    
$chart = ($sum > $sum_previous_month) ? "\n\xF0\x9F\x93\x88 +" : "\n\xF0\x9F\x93\x89";
$shift = round((($sum - $sum_previous_month)/$sum_previous_month)*100, 1);

$text .= $chart.$shift."%\n";

include('/home/tfbddmzi/public_html/kurovo/squad/rating_update.php');
$text .= $rating;

$sel=$mysqli->query("SELECT SUM(`distance`) AS `sum` FROM `activity` WHERE YEAR(`date`)=YEAR(NOW())");
$sel->data_seek(0);
$res=$sel->fetch_assoc();
$sum = $res['sum'];
$text .= "\nЗа 2019 год:\n";
$text .= "\xF0\x9F\x90\x8E ".round($sum, 1)." км\n";
$text .= "\xF0\x9F\x8C\x8D ".(round($sum/40075, 3)*100)."% экватора\n";
$text .= "\xF0\x9F\x8C\x9C ".(round($sum/384467, 3)*100)."% пути до Луны\n";


send_msg($text, $chat_id);
exit('ok');


?>