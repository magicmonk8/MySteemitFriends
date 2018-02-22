<html>
  <head>
    <title>Top Contributors - My Steemit Friends</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="jquery/jquery-3.2.1.min.js"></script>
    <script src="extensions/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css?4">
    <style>
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
	.bg-4 {
		background-color:#4E3D0B;
		color: white;
	}	
	
		.popover {
    background: black;
	color:white !important;
}
	.pop-content {
    color: white !important;    
}

    </style>

  </head>

  <body class="bg-4">   

<nav id="mynav" class="navbar navbar-expand-md navbar-dark">
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
   
    <div class="container-fluid bg-4 text-center" style="max-width:1000px;">

     

<h1>Top Contributors Ranking</h1>       

<br>

<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Search Username for Ranking</h3>

<br>
<form style='margin-bottom:0px;' method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">Steemit UserName: <input id="author" type="text" name="author" value="<? if ($_GET["author"]) { echo $_GET["author"];} ?>" autofocus>

<br><br>
<button class="btn btn-light" type="submit">Show Top Contributors</button> 
<br><br>

</form>

</div>

<br><br>


<?php

		
if ($_GET["author"]) {		

$author = $_GET["author"];
$author = filter_var($author, FILTER_SANITIZE_STRING);
// connect to SteemSQL database
include 'steemSQLconnect2.php';		

	
echo '<table id="bigtable" class="table table-sm table-striped" style="background-color:#0f4880;border:5px solid white">';

    

echo '<thead class="thead-default mobile"><tr><th style="text-align: center;">Ranking</th><th style="text-align: center;">Name</th><th style="text-align: center;">Contribution Amount ($)</th></tr></thead>';		
		

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

    // for printing ranking and striped rows.
	$rank=1;
    $rownum=0;
    // print the results. 

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 

		$voter=$row['voter'];
		$contribution=$row['contribution'];
		
		if ($contribution>0) {
// calculation of SP formula no longer used (done in SQL). Kept here for reference: $ownsp = $total_vesting_fund_steem * $ownvests / $total_vesting_shares;
		
// create striped rows		
	  if ($rownum%2==0) {echo '<tr>';} else {echo '<tr style="background-color:#0f3066">';}
		$rownum++;
      echo '<td style="text-align: center;">';

      echo $rank;

      $rank++;

      echo "</td><td style='text-align: center;'>";      

      echo $voter;      

      echo "</td><td style='text-align: center;'>";		

	  echo '$'.number_format($contribution,2);

      echo "</td></tr>";
    
	  }
	}

echo "</table>";



// terminate connectiion

unset($conn); unset($sth);

}

?>  



</div>


</body>


</html>