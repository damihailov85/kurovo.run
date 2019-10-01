<?php 

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');

$start = microtime(true);
$section = [100, 400, 1000, 1609, 3000, 5000, 10000, 21098, 42195];
$record = [];
$run = [];


$sel = $mysqli->query("SELECT `activity_id` FROM `activity` WHERE `activity`='running' AND `path`=1 AND `counted`=0 LIMIT 4");                                                   

if ($sel->num_rows>0) {
    for ($q = 0; $q < $sel->num_rows; $q++) {
        $sel->data_seek($q);
        $res = $sel->fetch_assoc();
        $mysqli->query("UPDATE `activity` SET `counted`=1 WHERE `activity_id`='".$res['activity_id']."'");
   
        $sel_path = $mysqli->query("SELECT * FROM `activity_detail` 
                                        WHERE `run_id`='".$res['activity_id']."' 
                                        ORDER BY `step_number`");                                                   

        if ($sel_path->num_rows>0) {
            for ($i = 0; $i < $sel_path->num_rows; $i++) {
                $sel_path->data_seek($i);
                $res_path = $sel_path->fetch_assoc();
        
                $run[count($run)] = array("distance" => $res_path['distance'],
                                            "duration" => $res_path['duration'],
                                            "step_id" => $res_path['step_id'],
                                            "step_number" => $res_path['step_number']
                                    );       
            }
        }

        for ($i = 0; $i < count($section); $i++) {
            for ($j = 0; $j < count($run); $j++) {
                $k = $j;
                $length = 0;
                $time = 0;

                while ($length < $section[$i]){
                    $length += $run[$k]['distance'];
                    $time += $run[$k]['duration'];
                
                    if ($k == count($run)-1)
                        break;
                    $k++;
                }

                if ($length >= $section[$i]) {
                
                    if (!isset($record[$i]['time'])){
                        $record[$i] = array("time" => $time,
                                            "step_id" => $run[$j]['step_id']);
                    }
                
                    if ($time < $record[$i]['time']){
                        $record[$i]['time'] = $time;
                        $record[$i]['step_id'] = $run[$j]['step_id'];
                    }
                }
            }
        }

        for ($i = 0; $i < count($record); $i++) {
            $query = "INSERT INTO `record` (`run_id`, `section`, `start_point`, `duration`)
                                VALUES ('".$res['activity_id']."', ".$section[$i].", '".$record[$i]['step_id']."', ".$record[$i]['time'].")";
            $mysqli->query($query);
        }
    }
}

$mysqli->close();

?>