<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);


$sql = "
;With cte as
(
select name, total_vests, RANK() over (order by total_vests DESC) As RankByVests
FROM
(
/* Join each proxy's vesting with other users' vests in one table, including witness votes, calculate sum of vests in each row, rank by sum of vests */

select g.name AS name, ISNULL(f.proxied_vests, 0 ) as proxied_vests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares))) AS own_vests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares)))+ISNULL(f.proxied_vests, 0 ) AS total_vests, g.witness_votes as witness_votes
FROM 
(
/* sum vesting_shares for each proxy */
select e.proxy AS proxy, sum(e.vesting_shares) as proxied_vests
FROM (
/* select accounts with a proxy and their vesting_shares */
select account, proxy, convert(float,Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares))) AS vesting_shares
from (
select name as account, proxy, vesting_shares
from Accounts
where proxy!=''
) d 
) e
where e.proxy != ''
GROUP BY e.proxy
) f
RIGHT JOIN 
(SELECT name, vesting_shares, witness_votes
from Accounts
WHERE proxy='') g
ON f.proxy = g.name
) h
)
SELECT name, total_vests, RankByVests
from cte
where name=:name;
	";



   

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);

    $sth->execute();


    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $total_vests=$row[1];
			
           $rank=$row[2];

        }


if ($total_vests) {
echo "<p>".$SteemitUser." has a witness voting power of ".number_format($total_vests / 1000000, 3)." million vests and is ranked at ".$rank." out of all users.</p>"; 

$page = ceil($rank/50);

echo "<p><a href=witnessvoting.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";
} else {echo "<p>".$SteemitUser." is not part of the ranking because they have proxied their voting power to: ";

		
$sql = "
select name, proxy
from Accounts
where name=:name";

		
 $sth = $conn->prepare($sql);
 $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);
 $sth->execute();
 $row=$sth->fetch(PDO::FETCH_ASSOC);
 $proxy = $row['proxy'];
 echo $proxy."</p>";		

}


        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

