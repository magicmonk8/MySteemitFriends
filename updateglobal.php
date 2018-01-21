 <?php



//step1

$cSession = curl_init(); 

//step2

curl_setopt($cSession,CURLOPT_URL,"https://api.steemjs.com/get_dynamic_global_properties");

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



$total_vesting_fund_steem =  $resultArray["total_vesting_fund_steem"];

$total_vesting_fund_steem = preg_replace('/[^0-9.]+/', '', $total_vesting_fund_steem);

echo $total_vesting_fund_steem;



$total_vesting_shares=$resultArray["total_vesting_shares"];

$total_vesting_shares = preg_replace('/[^0-9.]+/', '', $total_vesting_shares);

echo $total_vesting_shares;



$myfile = fopen("global.txt", "w") or die("Unable to open file!");

$txt = "total_vesting_fund_steem: ".$total_vesting_fund_steem."\n";

fwrite($myfile, $txt);

$txt = "total_vesting_shares: ".$total_vesting_shares."\n";;

fwrite($myfile, $txt);

fclose($myfile);








?>
