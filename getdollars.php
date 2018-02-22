 <?php

 

$permlink = $_GET["permlink"];

$author = $_GET["author"];

$primevoter=$_GET["primevoter"];



// store list of voters

$voterarray = array();



// store list of rshares

$rsharesarray=array();



// store number of voters

$length=0;



// store total rshares

$total=0;



// store article total_pending_payout

$payout=0;



// store author_payout

$authorpay=0;



// store curator pay

$curatorpay=0;





//step1

$cSession = curl_init(); 

//step2

curl_setopt($cSession,CURLOPT_URL,"https://api.steemjs.com/get_content?author=".$author."&permlink=".$permlink);

curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);

curl_setopt($cSession,CURLOPT_HEADER, false); 

//step3

$result=curl_exec($cSession);

//step4

curl_close($cSession);

// echo $result;



//step5

$resultArray = json_decode($result, true);



// print_r($resultArray);



$payout =  $resultArray["pending_payout_value"];

$payout = preg_replace('/[^0-9.]+/', '', $payout);

// echo $payout;



$authorpay = $resultArray["total_payout_value"];

$authorpay = preg_replace('/[^0-9.]+/', '', $authorpay);

$curatorpay = $resultArray["curator_payout_value"];

$curatorpay = preg_replace('/[^0-9.]+/', '', $curatorpay);



if ($payout<=0) {

   $payout = $authorpay+$curatorpay;

}



$activeVotes = $resultArray["active_votes"];

// print_r($activeVotes[0]);

$length=count($activeVotes);

// echo $length;



for ($x=0;$x<$length;$x++) {

        $voterarray[$x]=$activeVotes[$x]["voter"];

        $rsharesarray[$x] = $activeVotes[$x]["rshares"];

}



    

    $total=array_sum($rsharesarray); 

 //    echo "total rshares: ".$total."<br>";



// sort contributions in order

    array_multisort($rsharesarray,SORT_DESC,$voterarray);



echo "<p>Total Payout = $";

echo round($payout,3);

echo "</p>";  



$rank=array_search($primevoter,$voterarray);

$rank++;

echo "<b>".$primevoter."</b> is ranked <b>number ".$rank."</b> in the curators' list contributing <b>$";

$contribution=round($rsharesarray[$rank-1]/$total*$payout,3);

echo $contribution."</b><br><br>";



echo "<p>Curators' list ranked by amount contributed: </p>";



    for ($x=0;$x<$length;$x++) {

        $rank=$x+1;

        echo $rank.": ";

        

        echo '<a href="http://steemit.com/@'.$voterarray[$x].'">'.$voterarray[$x].'</a>: $';

        

        $contribution=round($rsharesarray[$x]/$total*$payout,3);

        echo $contribution."<br>";          

    }







/* print_r($voterarray);

print_r($rsharesarray);





$rshares=$activeVotes[0]["rshares"];

 echo $rshares;

*/



?>