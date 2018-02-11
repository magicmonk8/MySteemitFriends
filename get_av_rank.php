<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

// retrieve global values for calculating Steem Power
$my_file = fopen("global.txt",'r');
$total_vesting_fund_steem=fgets($my_file);
$total_vesting_fund_steem = preg_replace('/[^0-9.]+/', '', $total_vesting_fund_steem);
$total_vesting_shares=fgets($my_file);
$total_vesting_shares = preg_replace('/[^0-9.]+/', '', $total_vesting_shares);
fclose($my_file);

// amount of steem per vest (needed to convert vests to steem)
	
$steem_per_vest = round($total_vesting_fund_steem / $total_vesting_shares, 6, PHP_ROUND_HALF_UP);
	

// retrieve global values for calculating Steem Power	
$my_file = fopen("steemprice.txt",'r');
$steemprice=fgets($my_file);
$steemprice = preg_replace('/[^0-9.]+/', '', $steemprice);
fclose($my_file);


// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);

// SQL for finding rank of Steemit User for Own SP
$sql = "
;With cte as
(
select accountval, RANK() over (order by accountval DESC) AS RankBySBD, name
from
(
SELECT TOP 100 PERCENT name,  
(convert(float,balance)+convert(float,savings_balance)+convert(float, a.vesting_shares)*".$steem_per_vest.")*3.889+convert(float,sbd_balance)+convert(float,savings_sbd_balance) AS accountval

FROM
(SELECT name, 
Substring(balance,0,PATINDEX('%STEEM%',balance)) AS balance, 
Substring(savings_balance,0,PATINDEX('%STEEM%',savings_balance)) AS savings_balance, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)) AS vesting_shares,  Substring(sbd_balance,0,PATINDEX('%SBD%',sbd_balance)) AS sbd_balance, Substring(savings_sbd_balance,0,PATINDEX('%SBD%',savings_sbd_balance)) AS savings_sbd_balance
FROM Accounts (NOLOCK)) a
) b
)
select accountval, RankBySBD
from cte
where name=:name;

";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $sbd=$row[0];
			
           $rank=$row[1];

        }

	


echo "<p>".$SteemitUser." has an estimated account value of ".number_format(round($sbd))." and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=accountvalue.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";



        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

