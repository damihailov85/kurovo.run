<pre>
<?php

$list = [];
    $squad_url = 'https://api.squadeasy.com/api/v3/squad/squad/withmembers/';
    
    $payload = array( "squad_id" => "5a8bca301454a5262d7d0905" );

    include($path.'squad/request.php');

    foreach ($response["members"] as &$member) {
        
        for ($j = 0; $j < count($list); $j++){
            if ($member["_id"]==$list[$j]["sq"]){
                $list[$j]["check"] = 1;
                echo $list[$j]["name"]."<br/>";
            }
        }
        
        if ($member["profile"]["nb_boosts"] > 0) {
            
            foreach ($member["profile"]["boost_list"] as &$boost) {    
            
                $sq_time =  date("Y-m-d H:i:s", strtotime($boost["profile"]["boost_regenerated"]));
                $sel2 = $mysqli->query("SELECT * FROM `boost` WHERE `sender_squad_id`='".$boost["_id"]."'");
                echo $boost["_id"]."<br/>";
                if ($sel2->num_rows==0){
                    $query = "INSERT INTO `boost` (`sender_squad_id`, `recipient_squad_id`, `boost_death_time`) 
                                                 VALUES('".$boost["_id"]."', '".$member["_id"]."', '".$sq_time."')";
                    $ins = $mysqli->query($query);
                    echo $query."<br/>";
                }
            
                else {
                    $upd = $mysqli->query("UPDATE `boost` 
                                            SET `recipient_squad_id`='".$member["_id"]."', `boost_death_time`='".$sq_time."'
                                            WHERE `sender_squad_id`='".$boost["_id"]."'");
                }
            }
        }
        echo "<br/>-------------------------<br/>";

    }

?>

</pre>