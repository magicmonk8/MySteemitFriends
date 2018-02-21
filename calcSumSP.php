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
	
$topspnum = $_GET["topSPNum"];
$topspnum = filter_var($topspnum, FILTER_SANITIZE_NUMBER_INT);


$sql = "

;With q1 as 
(
SELECT top :topspnum name, convert(float, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)))*".$steem_per_vest." AS sp, 
convert(float, Substring(vesting_withdraw_rate,0,PATINDEX('%VESTS%',vesting_withdraw_rate)))*".$steem_per_vest." AS next_withdrawl_sp, 
next_vesting_withdrawal, 
b.maxtime as withdrawl_start_date
FROM Accounts (NOLOCK) a LEFT JOIN
(select account, max(timestamp) AS maxtime
from TxWithdraws (NOLOCK)
group by account
) b
ON a.name = b.account
order by sp DESC
)
select SUM(next_withdrawl_sp)
from q1

";
	
	

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':topspnum', $topspnum, PDO::PARAM_INT);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.



    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

		 $sum=$row[0];
	}

	echo "<p> The sum of SP being withdrawn is ".number_format(round($sum)).".</p>";

// terminate connectiion

unset($conn); unset($sth);

  

?>  

