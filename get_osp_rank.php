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

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);

// SQL for finding rank of Steemit User for Own SP
$sql = "
;With cte as
(
select vests, RANK() over (order by vests DESC) AS RankByOSP, name
from
(
SELECT TOP 100 PERCENT a.name AS name, convert(float, a.vesting_shares) AS vests
FROM
(SELECT name, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)) AS vesting_shares
FROM Accounts (NOLOCK)) a
Order by vests DESC
) b
)
select vests, RankByOSP
from cte
where name=:name;

";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $vests=$row[0];
			$sp = $total_vesting_fund_steem * $vests / $total_vesting_shares;
           $rank=$row[1];

        }

	


echo "<p>".$SteemitUser." owns ".number_format(round($sp))." SP and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=ownSP.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";



        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

