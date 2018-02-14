<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);


$sql = "
;With cte as
(
select proxy, total_vests, RANK() over (order by total_vests DESC) As RankByVests
FROM
(
select f.proxy AS proxy, f.proxied_vests AS proxiedvests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares))) AS own_vests, convert(float,Substring(g.vesting_shares,0,PATINDEX('%VESTS%',g.vesting_shares)))+f.proxied_vests AS total_vests
FROM 
(
select e.proxy AS proxy, sum(e.vesting_shares) as proxied_vests
FROM (
select c.account, c.proxy, convert(float,Substring(d.vesting_shares,0,PATINDEX('%VESTS%',d.vesting_shares))) AS vesting_shares
from (select a.* 
from TxAccountWitnessProxies a 
INNER JOIN 
(
SELECT account, MAX(timestamp) AS maxtime
FROM TxAccountWitnessProxies
GROUP BY account
) b 
ON a.account=b.account
AND a.timestamp= b.maxtime
) c INNER JOIN (
select name, vesting_shares
from Accounts) d
ON c.account = d.name
) e
where e.proxy != ''
GROUP BY e.proxy
) f
INNER JOIN 
(SELECT name, vesting_shares
from Accounts) g
ON f.proxy = g.name
) h
)
SELECT proxy, total_vests, RankByVests
from cte
where proxy=:name;
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
echo "<p>".$SteemitUser." has a witness voting power of ".number_format($total_vests / 1000000, 3)." million vests and is ranked at ".$rank." out of all witness proxies.</p>"; 




$page = ceil($rank/50);

echo "<p><a href=witnessproxies.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";
} else {echo "<p>".$SteemitUser." is not a proxy.</p>";}


        



// terminate connectiion

unset($conn); unset($sth);

  

?>  

