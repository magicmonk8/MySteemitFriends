 <?php



//step1

$cSession = curl_init(); 

//step2

curl_setopt($cSession,CURLOPT_URL,"https://api.coinmarketcap.com/v1/ticker/steem-dollars/?convert=USD");

curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);

curl_setopt($cSession,CURLOPT_HEADER, false); 

//step3

$result=curl_exec($cSession);

//step4

curl_close($cSession);

// echo $result;



//step5

$resultArray = json_decode($result, true);

$steem_price =  $resultArray[0]["price_usd"];

$steem_price = preg_replace('/[^0-9.]+/', '', $steem_price);

echo $steem_price;


$myfile = fopen("CMCsteemprice.txt", "w") or die("Unable to open file!");

$txt = "steem_price: ".$steem_price."\n";

fwrite($myfile, $txt);

fclose($myfile);

?>