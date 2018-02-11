 <?php



//step1

$cSession = curl_init(); 

//step2

curl_setopt($cSession,CURLOPT_URL,"https://api.steemjs.com/get_current_median_history_price");

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



$steem_price =  $resultArray["base"];

$steem_price = preg_replace('/[^0-9.]+/', '', $steem_price);

echo $steem_price;


$myfile = fopen("steemprice.txt", "w") or die("Unable to open file!");

$txt = "steem_price: ".$steem_price."\n";

fwrite($myfile, $txt);

fclose($myfile);

?>