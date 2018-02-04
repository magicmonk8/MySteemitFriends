
<?php


include 'steemSQLconnect2.php';		

	


$SteemitUser = $_GET["SteemitUser"];


// Use try catch exception handling. Details: https://www.w3schools.com/PhP/php_exception.asp


    $sql = "

;With cte as

(


SELECT TOP 100 PERCENT name, cast(log10(reputation)*9 - 56 as decimal(4,2)) as rep, RANK() over (order by cast(log10(reputation)*9 - 56 as decimal(4,2)) DESC) AS RankByReputation
FROM Accounts
ORDER BY rep DESC

)

select rep, RankByReputation 

from cte 

where name = '".$SteemitUser."'

";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth->execute();

    

    $reputation;

    $rank;

    

    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $reputation=$row[0];

           $rank=$row[1];

        }



echo "<p>".$SteemitUser." has a reputation of ".$reputation." and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=reputation.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";

    

// terminate connectiion

unset($conn); unset($sth);

  

?>  

