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
select total_payout, RANK() over (order by total_payout DESC) AS RankByPP, author AS name
from
(SELECT
author, sum(pending_payout_value) AS total_payout
FROM Comments
WHERE parent_author='' AND created >= '".date("Y-m-d", strtotime("-8 days"))."' AND created < '".date("Y-m-d")."' AND pending_payout_value !=0
GROUP BY author

) a
)
select total_payout, RankByPP
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

echo "<p>".$SteemitUser." has a pending payout value of ".number_format(round($pp))." and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=pending_payout.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";

} else {echo "<p>".$SteemitUser." has no pending payout and is therefore not on this ranking. </p>";}

        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

