<?php

$photo = $arr['message']['photo'];
// Берем id файла. Их несколько приходит, в разных размерах. Последний типа самый чОткий.
$file_id = $photo[count($photo) - 1]['file_id'];
// По id получаем путь к файлу от телеги
$path = file_get_contents('https://api.telegram.org/bot'.$token.'/getFile?file_id='.$file_id);
$path = json_decode($path, true); 

if (isset($path['result']['file_path'])){
    $file_path = $path['result']['file_path'];
}
$file_from_tgrm = "https://api.telegram.org/file/bot".$token."/".$file_path;
// достаем расширение файла
$ext =  end(explode(".", $file_path));

$name_our_new_file = date("Y-m-d-H-i-s").".".$ext;
copy($file_from_tgrm, "img/".$name_our_new_file);

// Имя - в базу
$ins = $mysqli->query("INSERT INTO `week_mission` (`img`) VALUES ('".$name_our_new_file."') ");

// Берем имя предшественника
$sel = $mysqli->query("SELECT * FROM `week_mission` ORDER BY `date`");
$sel->data_seek(0);
$res = $sel->fetch_assoc();
// Удаляем. В Турции было всего 200Мб, на Украине не актуально, но пофиг
unlink('img/'.$res['img']);
// ..и запись из базы
$del = $mysqli->query("DELETE FROM `week_mission` WHERE `id`=".$res['id']);


$text = "Загружено!";
file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=HTML&chat_id='.$user_id.'&text='.$text);
$sel = $mysqli->query("SELECT * FROM `week_mission` ORDER BY `date` DESC");
$sel->data_seek(0);
$res = $sel->fetch_assoc();
$week_mission = $main_page.'img/'.$res['img'];
file_get_contents('https://api.telegram.org/bot'.$token.'/sendPhoto?chat_id='.$user_id.'&photo='.$week_mission);


exit('ok');

?>