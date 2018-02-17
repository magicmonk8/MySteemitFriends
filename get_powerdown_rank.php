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
	

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);



	$sql = "
	
;With cte as	
(
select name, next_withdrawl_sp, RANK() over (order by next_withdrawl_sp DESC) AS RankByPD
FROM
(
SELECT name, convert(float, Substring(vesting_withdraw_rate,0,PATINDEX('%VESTS%',vesting_withdraw_rate)))*".$steem_per_vest." AS next_withdrawl_sp
FROM Accounts (NOLOCK)
where vesting_withdraw_rate NOT LIKE '0.0000%'
) a
)
select next_withdrawl_sp, RankByPD
from cte
where name=:name;

	";


    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $next_withdrawl_sp=$row[0];
			
           $rank=$row[1];

        }

	
if ($next_withdrawl_sp) {

echo "<p>".$SteemitUser." is losing ".number_format(round($next_withdrawl_sp))." SP per power down and is ranked at  ".$rank.".</p>";


$page = ceil($rank/50);

echo "<p><a href=powerdown.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";

} else {
	
	echo "<p>".$SteemitUser." is not powering down. </p>";
}

        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

