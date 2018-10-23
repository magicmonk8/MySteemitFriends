<?php   
// connect to SteemSQL database
include 'steemSQLconnect2.php';
// sanitize input for Steemit UserName
$SteemitUser = $_GET["SteemitUser"];
$SteemitUser = filter_var($SteemitUser, FILTER_SANITIZE_STRING);
// SQL for finding rank of Steemit User for Own SP
$sql = "
;With ranktable as
(
select created, RANK() over (order by created ASC, name ASC) AS RankByCreation, name
from
(
SELECT name, created
FROM Accounts (NOLOCK)
) b
)
select created, RankByCreation
from ranktable
where name=:name
";
// execute the query. Store the results in sth variable.
$sth = $conn->prepare($sql);
$sth -> bindValue(':name', $SteemitUser, PDO::PARAM_STR);
$sth->execute();
// print the results. If successful, magicmonk will be printed on page.
while ($row = $sth->fetch(PDO::FETCH_NUM)) {
$creation=$row[0];
$rank=$row[1];
}
echo "<p>".$SteemitUser." was created on ".$creation." and is ranked at  ".$rank.".</p>";
$page = ceil($rank/50);
echo "<p><a href=ranking.php?mode=accountCreation&page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";
// terminate connectiion
unset($conn); unset($sth);
?>  

