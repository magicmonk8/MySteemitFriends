<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);


// retrieve input for ranking option - all users by default, or witness voter only (votes casted >0)
if ($_GET["rankopt"]) { 
		$rankopt = $_GET["rankopt"];
} else {$rankopt='allusers';}


$sql = "
;With cte as
(
select name, total_vests, RANK() over (order by total_vests DESC) As RankByVests, witnesses_voted_for
FROM
(
/* Join each proxy's vesting with other users' vests in one table, including witness votes, calculate sum of vests in each row, rank by sum of vests */

select g.name AS name, ISNULL(f.proxied_vests, 0 ) as proxied_vests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares))) AS own_vests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares)))+ISNULL(f.proxied_vests, 0 ) AS total_vests, g.witness_votes as witness_votes,
g.witnesses_voted_for as witnesses_voted_for
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
(SELECT name, vesting_shares, witness_votes, witnesses_voted_for
from Accounts
WHERE proxy='') g
ON f.proxy = g.name";
if ($rankopt=="wvonly") {
		// only insert this SQL if on witness voters only mode. 
$sql.="
where witnesses_voted_for>0";		
}
$sql.="
) h
)
SELECT name, total_vests, RankByVests, witnesses_voted_for
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
		
		  $witnesses_voted_for=$row[3];
	

        }


if ($total_vests) {
echo "<p>".$SteemitUser." has a witness voting power of ".number_format($total_vests / 1000000, 3)." million vests and is ranked at ".$rank.".</p>"; 

$page = ceil($rank/50);

echo "<p><a href=witnessvoting.php?rankopt=".$rankopt."&page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";
} else {
	
	$sql = "
	select name, proxy
	from Accounts
	where name=:name";
		
	 $sth = $conn->prepare($sql);
	 $sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);
	 $sth->execute();
	 $row=$sth->fetch(PDO::FETCH_ASSOC);
	 $proxy = $row['proxy'];	

	if ($proxy!=" ") {
	echo "<p>".$SteemitUser." is not part of the ranking because they have proxied their voting power to: ";
	 echo $proxy."</p>";
		
	} else {
		echo "<p>".$SteemitUser." is not part of the ranking because they have not casted any witness votes";		
	}
        
}




// terminate connectiion

unset($conn); unset($sth);

  

?>  

