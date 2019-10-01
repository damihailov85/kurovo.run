<pre>
<?php

$arr = [];
$rank = [[1,1],[2,9],[10,29],[30,59],[60,99]];

$squad_url = 'https://api.squadrunner.co/api/v3/battle/ranking/';


for ($i=0; $i<count($rank); $i++) {
                                                                
    $payload = array(
                "company_id"=>"5a1554b82c1350cbd9afbade", 
                "max_rank"=> $rank[$i][1],
                "min_rank"=> $rank[$i][0],
                "sort"=> "rank_league", 
                "sortAsc"=> "true", 
                "user_id"=> $squad_id_to_curl
            ); 

    include('squad/request.php');


    foreach ($response as $key=>$value) {

        if ($response[$key]['_id'] == "5a8bca301454a5262d7d0905") {
            $kurovo = round($response[$key]['points_league']/100);
            $kurovo_rank = $response[$key]['rank_league'];
        } 
        if ($response[$key]['rank_league'] == "9"){
            $diamond_down = round($response[$key]['points_league']/100);
        }   
        if ($response[$key]['rank_league'] == "10"){
            $gold_up = round($response[$key]['points_league']/100);
        }  
        if ($response[$key]['rank_league'] == "29"){
            $gold_down = round($response[$key]['points_league']/100);
        }  
        if ($response[$key]['rank_league'] == "30"){
            $silver_up = round($response[$key]['points_league']/100);
        }  
        if ($response[$key]['rank_league'] == "59"){
            $silver_down = round($response[$key]['points_league']/100);
        }  
        if ($response[$key]['rank_league'] == "60"){
            $bronze_up = round($response[$key]['points_league']/100);
        }  
    }
}

$rating = "\nСквад:\n";

if($kurovo_rank<10){
    $rating .= "\xF0\x9F\x92\x8E ".$kurovo." (сейчас ".$kurovo_rank.")\n";
    $rating .= "\xF0\x9F\xA5\x87 ".($gold_up - $kurovo)." (".round(($gold_up-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x88 ".($silver_up - $kurovo)." (".round(($silver_up-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x89 ".($bronze_up - $kurovo)." (".round(($bronze_up-$kurovo)*100/$kurovo, 1)."%)\n";
}

if($kurovo_rank>9&&$kurovo_rank<30){
    $rating .= "\xF0\x9F\x92\x8E +".($diamond_down - $kurovo)." (+".round(($diamond_down-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x87 ".$kurovo." (сейчас ".$kurovo_rank.")\n";
    $rating .= "\xF0\x9F\xA5\x88 ".($silver_up - $kurovo)." (".round(($silver_up-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x89 ".($bronze_up - $kurovo)." (".round(($bronze_up-$kurovo)*100/$kurovo, 1)."%)\n";
}

if($kurovo_rank<60&&$kurovo_rank>29){
    $rating .= "\xF0\x9F\xA5\x87 +".($gold_down - $kurovo)." (+".round(($gold_down-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x88 ".$kurovo." (сейчас ".$kurovo_rank.")\n";
    $rating .= "\xF0\x9F\xA5\x89 ".($bronze_up - $kurovo)." (".round(($bronze_up-$kurovo)*100/$kurovo, 1)."%)\n";
}

if($kurovo_rank>59){
    $rating = "\nСквад:\n";
    $rating .= "\xF0\x9F\xA5\x87 +".($gold_down - $kurovo)." (+".round(($gold_down-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x88 +".($silver_down - $kurovo)." (+".round(($silver_down-$kurovo)*100/$kurovo, 1)."%)\n";
    $rating .= "\xF0\x9F\xA5\x89 ".$kurovo." (сейчас ".$kurovo_rank.")\n";
}

?>

</pre>