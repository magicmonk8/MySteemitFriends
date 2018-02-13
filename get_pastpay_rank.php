<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);


// SQL for finding rank of Steemit User for Own SP
$sql = "
;With cte as
(
select sbd_paid, RANK() over (order by sbd_paid DESC) AS RankByPP, author AS name
from
(select 
author, sum(sdb_payout) AS sbd_paid, sum(vesting_payout) AS vests_paid 
From VOAuthorRewards
where timestamp >= '".date("Y-m-d", strtotime("-30 days"))."'
GROUP BY author
) a
)
select sbd_paid, RankByPP
from cte
where name=:name;

";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $pp=$row[0];
			
           $rank=$row[1];

        }

	
if ($pp>0) {

echo "<p>".$SteemitUser." has a SBD past payout value of ".number_format(round($pp))." and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=past_payout.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";

} else {echo "<p>".$SteemitUser." has no 30-day past payout and is therefore not on this ranking. </p>";}

        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

