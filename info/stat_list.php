<?php

$text = "Километры:\nза текущий месяц - /stat_c_m_d_ \nза текущий год - /stat_c_y_d_ \nза прошлый месяц - /stat_p_m_d_ \nза прошлый год - /stat_p_y_d_ \n\n";
$text .= "Количество пробежек:\nза текущий месяц - /stat_c_m_c_ \nза текущий год - /stat_c_y_c_ \nза прошлый месяц - /stat_p_m_c_ \nза прошлый год - /stat_p_y_c_ \n\n";
$text .= "Темп:\nза текущий месяц - /stat_c_m_p_ \nза текущий год - /stat_c_y_p_ \nза прошлый месяц - /stat_p_m_p_ \nза прошлый год - /stat_p_y_p_ \n\n";
$text .= "Время:\nза текущий месяц - /stat_c_m_t_ \nза текущий год - /stat_c_y_t_ \nза прошлый месяц - /stat_p_m_t_ \nза прошлый год - /stat_p_y_t_ \n\n";
$text = urlencode($text);
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$chat_id.'&text='.$text);

?>