<pre>
<?php

include('/home/oqduzwlo/public_html/kurovo/tool/config.php');
$start = microtime(true);

$sel = $mysqli->query("SELECT `activity_id`, `squad_id` FROM `activity` WHERE `activity`='running' AND `path`=0 ");                                                   


if ($sel->num_rows>0) {
    $c = ($sel->num_rows > 100) ? 100 : $sel->num_rows;
    for ($i = 0; $i < $c ; $i++) {
    
        $sel->data_seek($i);
        $res = $sel->fetch_assoc();

        $squad_url = 'https://api.squadeasy.com/api/v3/runner/run/';
        $payload = array("run_id" => $res['activity_id'], "user_id" => $res['squad_id']); 

        include('/home/oqduzwlo/public_html/kurovo/squad/request.php');

        $points = [];

        foreach($response['path'] as $num => $point){

            $points[count($points)] = "('".$res['activity_id']."',".
                                                "'".$point['_id']."',".
                                                $num.",".
                                                $point['altitude'].",".
                                                $point['distance'].",".
                                                $point['duration'].",".
                                                $point['latitude'].",".
                                                $point['longitude'].",".
                                                $point['speed'].
                                                ")";
        }

        $values = "";
        for ($j = 0; $j < count($points); $j++) {
            $values .= $points[$j];
            if (($j%100 != 0 && $j != count($points) - 1)|| $j==0)
                $values .= ",";
            if (($j%100 == 0 && $j != 0) || $j == count($points) - 1) {
                $query = "INSERT INTO `activity_detail` 
                            (`run_id`, `step_id`, `step_number`, `altitude`, `distance`, `duration`, `latitude`, `longitude`, `speed`)
                            VALUES ".$values;
                $mysqli->query($query);
                $values = "";
            }
                            
        }

        $mysqli->query("UPDATE `activity` SET `path`=1 WHERE `activity_id`='".$res['activity_id']."'");
        echo microtime(true) - $start;
        echo "<br/>";
        
    }
} 
echo "<br/>";
echo "<br/>";
echo microtime(true) - $start;

$mysqli->close();

?>

</pre>
