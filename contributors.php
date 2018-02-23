<html>
<?php
  if ($_GET["author"]) { 
	  $author = $_GET["author"];
  }
?>
  <head>
    <title><?if ($author) {echo $author."'s ";} ?>Top Contributors - My Steemit Friends</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="jquery/jquery-3.2.1.min.js"></script>
    <script src="extensions/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css?4">
    <style>

    td {
      text-align: center;
	  background-color:#1F1704;
    }

    a.page-link{
      color:blue !important;
    }
		
    a.page-link:visited {
      color:blue !important;
    }
		
    ul {
      margin:0.5rem;
    }
		
    ul.navbar-nav {
       margin:0px;
    }

    a.btn-info, a.btn-info:visited, a.btn-primary, a.btn-primary:visited {
       color:white !important;
    } 

    a.btn-light {
      color:blue !important;
    }

    a.btn-light:visited {
      color:blue !important;
    }
		
    .navbutton {
		width:10rem;
		margin:1rem;
     }

	/*background color */
    .bg {
      background-color:#4E3D0B;
      color: white;
     }	

   </style>

  </head>

  <body class="bg">   

<nav id="mynav" class="navbar navbar-expand-lg navbar-dark">
  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>  
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
     <a class="btn btn-lg btn-warning navbutton nounderline"  href="contributors.php" style="color:black">Contributors</a>
     <a class="btn btn-lg btn-primary navbutton nounderline"  href="index.php">Upvote Stats</a>
    <a class="btn btn-lg btn-success navbutton nounderline"  href="conversation.php">Conversations</a>
    <div class="btn-group navbutton" id="rankingbtn">
    <button type="button" class="btn btn-lg btn-info dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:10rem">Rankings</button>
    <div class="dropdown-menu">
    	<a class="dropdown-item" href="followers.php">Followers</a>			
		<a class="dropdown-item" href="reputation.php">Reputation</a>
		<a class="dropdown-item" href="effectiveSP.php">Effective SP</a>
		<a class="dropdown-item" href="ownSP.php">Own SP</a>
		<a class="dropdown-item" href="sbd.php">SBD</a>	
		<a class="dropdown-item" href="accountvalue.php">Estimated Account Value</a>     
   		<a class="dropdown-item" href="pending_payout.php">Pending Payout</a>
   		<a class="dropdown-item" href="past_payout.php">Past Payout</a>  
   		<a class="dropdown-item" href="powerdown.php">Power Down</a> 
   		<a class="dropdown-item" href="witnessvoting.php">Witness Voting Power: All Users</a>          
   		<a class="dropdown-item" href="witnessproxies.php">Witness Voting Power: Proxies</a> 
    </div>
  </div><!-- /btn-group -->
    <a class="btn btn-lg btn-danger navbutton nounderline"  href="upvotelist.php">$ Calculator</a>
  </div> 
</nav>  
   
<div class="container-fluid bg text-center">
<div class="row">
<div class="col-lg">

<h1>All Contributors Ranking</h1><br>

<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Show contributors for:</h3><br>

<form style='margin-bottom:0px;' method="get" action="<? echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">Steemit UserName: <input id="author" type="text" name="author" value="<? if ($author) { echo $author;} ?>" autofocus>
<br><br>
<button class="btn btn-light" type="submit">Show contributors</button> 
<br><br>
</form>

</div>

<br><br>

<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Search for user in contributor table:</h3><br>

Search for user: <input type="text" id="searchName" onkeyup="searchFunction()">

<br><br>

</div>
<br><br>
</div><div class="col-lg">

<?php

		
if ($author) {		

$author = filter_var($author, FILTER_SANITIZE_STRING);
// connect to SteemSQL database
include 'steemSQLconnect2.php';		

	
echo '<table id="bigtable" class="table table-sm table-striped" style="background-color:#0f4880;border:5px solid white">';

    

echo '<thead class="thead-default mobile"><tr><th style="text-align: center;">Ranking</th><th style="text-align: center;">Name</th><th style="text-align: center;">Contribution Amount</th></tr></thead>';		
		

    $sql = "
	
;With rsharequery AS 
(
SELECT C.created, C.author AS author, C.ID AS ID, C.total_payout_value,C.curator_payout_value, Votes.voter, Votes.rshares
FROM   Comments AS C  
          CROSS APPLY  
     OPENJSON (C.active_votes)  
           WITH (  
              voter   varchar(200) '$.voter',   
			  rshares BigInt '$.rshares'
           )  
  		AS Votes
WHERE C.author=:name AND C.parent_author=''
), 

RShareSum AS
(
select ID, sum(rshares) as sumRShares
from (
SELECT C.created, C.author AS author, C.ID, C.total_payout_value,C.curator_payout_value, Votes.voter, Votes.rshares
FROM   Comments AS C  
          CROSS APPLY  
     OPENJSON (C.active_votes)  
           WITH (  
              voter   varchar(200) '$.voter',   
			  rshares BigInt '$.rshares'
           )  
  		AS Votes
WHERE C.author=:name AND C.parent_author=''
) AS a
group by ID
), 

payouts AS
(
select ID, total_payout_value+curator_payout_value+pending_payout_value AS payout
from Comments C
WHERE C.author=:name AND C.parent_author=''

)

select voter, round(sum(contribution),2) AS contribution
FROM
(select voter, CAST(rshares AS float) / CAST(sumRshares AS float) * payout AS contribution
FROM
(select voter, rshares, a.ID, b.sumRshares, c.payout
from rsharequery a, RshareSum b, payouts c
where a.ID = b.ID
AND a.ID = c.ID
) d
) e
group by voter
order by contribution DESC
";


// prepare the SQL statement, then bind value to variables, this prevents SQL injection.
    $sth = $conn->prepare($sql);
    $sth -> bindValue(':name', $author, PDO::PARAM_STR);
    $sth->execute();

// variables for printing ranking and striped rows.
    $rank=1;
    $rownum=0;

// store result in json object
    $rows = array();

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 

// only add to json array if contribution larger than 0

      $row['contribution']=number_format($row[contribution],2);
       
      if ($row['contribution']>0) {
      $row['rank']=$rank;
      $rank++;         
      $rows[] = $row;
      }
  }
	

echo "</table>";

// terminate connectiion

unset($conn); unset($sth);

}

?>  

</div>
</div>
</div>
<script>
// output to table from JSON string
var ar = <? echo json_encode($rows) ?>;
console.log(ar);

for(var i=0;i<ar.length;i++) {

        var tr="<tr>";
        var td1="<td>"+ar[i]["rank"]+"</td>";
        var td2="<td>"+'<a href="http://steemit.com/@'+ar[i]["voter"]+'">'+ar[i]["voter"]+"</a></td>";
        var td3="<td>"+'<a href="http://mysteemitfriends.online/contributors.php?author='+ar[i]["voter"]+'">$'+ar[i]["contribution"]+"</a></td></tr>";

       $("#bigtable").append(tr+td1+td2+td3); 

}

function searchFunction(){

// retrieve content from textbox 
   textboxval=$("#searchName").val();

// remove all rows in the table (all rows except first) 
  $("#bigtable").find("tr:gt(0)").remove();

  for(var i=0;i<ar.length;i++) {
        if (ar[i]["voter"].includes(textboxval)) {
			
		var tr="<tr>";
        var td1="<td>"+ar[i]["rank"]+"</td>";
        var td2="<td>"+'<a href="http://steemit.com/@'+ar[i]["voter"]+'">'+ar[i]["voter"]+"</a></td>";
        var td3="<td>"+'<a href="http://mysteemitfriends.online/contributors.php?author='+ar[i]["voter"]+'">$'+ar[i]["contribution"]+"</a></td></tr>";
        $("#bigtable").append(tr+td1+td2+td3); 
     }
  }
}
</script>
</body>
</html>