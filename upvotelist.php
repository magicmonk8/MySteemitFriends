                                                                     <html>

<head>
<title>My Steemit Friends</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<script src="jquery/jquery-3.2.1.min.js"></script>

<script src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">




</head>

<body>

<div class="container-fluid bg-1 text-center">

<?php
$author = rtrim($_GET["author"]);
$voter = rtrim($_GET["voter"]);
echo '<p><a href="http://steemit.com/@'.$voter.'"<b>'.$voter.'</b></a> upvoted <a href="http://steemit.com/@'.$author.'"><b>'.$author.'</b></a> on the following:</p>';
$servername = "sql.steemsql.com:1433";
$username = "steemit";
$password = "steemit";

   if ($_GET["Months"]) {
    $months = $_GET["Months"];
    } else {
    $months = 3;
    }

    if ($_GET["Articlesonly"]) {
    $articlesonly = $_GET["Articlesonly"];
    } else {
    $articlesonly = 1;
    }

try {
    $conn = new PDO("dblib:host=$servername;dbname=DBSteem", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // cast(body as text) needed to avoid error Connection failed: SQLSTATE[HY000]: General error: 20018 Unicode data in a Unicode-only collation or ntext data cannot be sent to clients using DB-Library (such as ISQL) or ODBC version 3.7 or earlier. [20018] (severity 16) [(null)]
    // if articles and comments are included
if ($articlesonly==1) {
 if ($months!="all") {
    $newdate = date("Y-m-d", strtotime("-".$months." months"));
    $sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (timestamp>=Convert(datetime, '".$newdate."')) ORDER BY timestamp DESC";
    } else {
    $sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') ORDER BY timestamp DESC";
    }
    }
    
    else {
    // if articles only, no comments
   if ($months!="all") {
    $newdate = date("Y-m-d", strtotime("-".$months." months"));
    $sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (timestamp>=Convert(datetime, '".$newdate."')) AND (permlink IN (SELECT permlink FROM Comments WHERE author='".$author."' AND depth=0)) ORDER BY timestamp DESC";
    } else {
    $sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (permlink IN (SELECT permlink FROM Comments WHERE author='".$author."' AND depth=0)) ORDER BY timestamp DESC";
    }
   }
    
    
    $sth = $conn->prepare($sql);
    $sth->execute();    
     echo '<table class="table table-sm"><thead class="thead-inverse"><tr><th>Timestamp</th><th>%</th><th>Link</th></tr></thead><tbody>';
     
     // row number of $ button
     $rownum=0;
     
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td>";
      echo $row[2];
      echo "</td><td>";
      echo $row[3]/100;
      echo "</td><td>";
      echo '<p><a href="https://steemit.com/cn/@'.$author.'/'.$row[1].'" target="_top">'.$row[1].'</a></p>';      
      echo '<div id='.$rownum.'><button type="button" class="btn btn-info" onClick="hello('.$rownum.',\''.$row[1].'\')">Show Contribution Ranking</button></div><br>';
      $rownum++;
      echo "</td></tr>";
      
    }
      
  echo "</tbody></table>";
}
    
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
    
    unset($conn); unset($sth);
    
?>

</div>

<script>
function hello(x,y) {
	var id=x;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState==1) {
     document.getElementById(id).innerHTML="loading..";
    }     
		if (this.readyState==4 && this.status==200) {
		document.getElementById(id).innerHTML=this.responseText;
		}	
	};
	xhttp.open("GET", "getdollars.php?author=<?php echo $author ?>&primevoter=<?php echo $voter ?>&permlink="+y, true);
	xhttp.send();	
}

</script>





</body>



</html>
