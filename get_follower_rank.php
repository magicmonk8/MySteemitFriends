

<?php



$SteemitUser = $_GET["SteemitUser"];

$servername = "sql.steemsql.com:1433";

$username = "steemit";

$password = "steemit";

// Use try catch exception handling. Details: https://www.w3schools.com/PhP/php_exception.asp

try {

    // connect to SteemSQL via PDO. Make sure pdo and pdo_dblib extensions are enabled.

    $conn = new PDO("dblib:host=$servername;dbname=DBSteem", $username, $password);

    

    // set the PDO error mode to exception

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    

    // print connection successful message if connection successful.



    // test query. Select the name column from the Accounts table where the Id is 29666. Result should be magicmonk.

    $sql = "

;With cte as

(

select TOP 100 PERCENT Following, count(*) AS Followers, RANK() over (order by count(*) DESC) AS RankByFollowers

from Followers

group by following

Order by Followers DESC



)

select Followers, RankByFollowers 

from cte 

where Following = '".$SteemitUser."'

";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth->execute();

    

    $followers;

    $rank;

    

    // print the results. If successful, magicmonk will be printed on page.

    while ($row = $sth->fetch(PDO::FETCH_NUM)) {

           $followers=$row[0];

           $rank=$row[1];

        }



echo "<p>".$SteemitUser." has ".$followers." followers and is ranked at  ".$rank.".</p>";



$page = ceil($rank/50);

echo "<p><a href=followers.php?page=".$page."&highlight=".$SteemitUser.">".$SteemitUser." is on page ".$page.". Click to see this page</a>.</p>";



        

}

// if cannot connect to database, print error message    

catch(PDOException $e)  {

    echo "Connection failed: " . $e->getMessage();

}

// terminate connectiion

unset($conn); unset($sth);

  

?>  

