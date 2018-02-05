<html>

<head>
<title>My Steemit Friends</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<script src="jquery/jquery-3.2.1.min.js"></script>
<script src="popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css?2">
</head>

<body>

<nav id="mynav" class="navbar navbar-expand-sm navbar-dark">
  <span class="navbar-brand mb-0 h1">Tools by <a href="http://steemit.com/@magicmonk">@magicmonk</a></span>
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" href="index.php">Upvote Statistics</a>
    </li>
    
    <!-- Dropdown menu for ranking -->
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Ranking tables</a>
    <div class="dropdown-menu">
    	<a class="dropdown-item" href="followers.php">Followers Ranking</a>
    	<a class="dropdown-item" href="effectiveSP.php">Effective SP Ranking</a>
    	<a class="dropdown-item" href="reputation.php">Reputation Ranking</a>     	
    </div>    
    </li>
    
    <li class="nav-item">
      <a class="nav-link" href="conversation.php">Conversation Record</a>
    </li>
  
  </ul>
</nav>     
    

<div class="container-fluid bg-1 text-center">

<div id="total_con" style="padding-top:1rem;padding-left:1rem;padding-right:1rem;border: 5px solid white; max-width:400px;margin:auto;display:none;margin-bottom:1rem;"></div>

<?php

// connection to SteemSQL database. See https://github.com/Bulletproofmonk/PHPSteemSQL/blob/master/connectv7.php
include 'steemSQLconnect2.php';		
	
// list of article addresses for contribution calculator later.	
$articleList=array();

// start putting addresses in the list at 0 element
$articleIndex=0;

// author and voter details stripped from URL
$author = rtrim($_GET["author"]);
$voter = rtrim($_GET["voter"]);

	
// if number of months not given, choose 3 as default
if ($_GET["Months"]) {
	$months = $_GET["Months"];
} else {
	$months = 3;
}

// retrieve choice for whether to include articles only or to include comments as well
if ($_GET["Articlesonly"]) {
	$articlesonly = $_GET["Articlesonly"];
} else {
	$articlesonly = 1;
}
	
// look up how much the author contributed to the voter	
echo '<p><a href="upvotelist.php?author='.$voter.'&voter='.$author.'&Months='.$months.'&Articlesonly='.$articlesonly.'"><b>Reverse Lookup</b>: how much has <b>@'.$author.'</b> contributed to <b>@'.$voter.'?</b></a></p>';	

	
// title at the top of page to state the voter and who is the author	
echo '<p><a href="http://steemit.com/@'.$voter.'"><b>@'.$voter.'</b></a> upvoted <a href="http://steemit.com/@'.$author.'"><b>@'.$author.'</b></a> on the following:</p>';	
	



		
	
// SQL executed if articles and comments are included
if ($articlesonly==1) {
	if ($months!="all") {
		$newdate = date("Y-m-d", strtotime("-".$months." months"));
    	$sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (timestamp>=Convert(datetime, '".$newdate."')) ORDER BY timestamp DESC";
    } else {
		$sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') ORDER BY timestamp DESC";
    }
} else {
    // if articles only, no comments
	if ($months!="all") {
		$newdate = date("Y-m-d", strtotime("-".$months." months"));
		$sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (timestamp>=Convert(datetime, '".$newdate."')) AND (permlink IN (SELECT permlink FROM Comments WHERE author='".$author."' AND depth=0)) ORDER BY timestamp DESC";
    } else {
    	$sql = "SELECT voter,permlink,timestamp,weight FROM TxVotes WHERE (author='$author' AND voter='$voter') AND (permlink IN (SELECT permlink FROM Comments WHERE author='".$author."' AND depth=0)) ORDER BY timestamp DESC";
    }
}
    
// prepares the SQL statement to be executed.    
$sth = $conn->prepare($sql);

// execute SQL statement.	
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
// store URL of articles in articleList array
	  $articleList[$articleIndex]=$row[1];
	  $articleIndex++;
      echo '<div id='.$rownum.'><button type="button" class="btn btn-info" onClick="showContribution('.$rownum.',\''.$row[1].'\')">Show Contribution Ranking & Add to Calculator</button></div><br>';
      $rownum++;
      echo "</td></tr>";
      
    }
      
  echo "</tbody></table>";

    unset($conn); unset($sth);
    
?>

</div>

</body>

<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

<script>

steem.api.setOptions({ url: 'https://api.steemit.com'});	

// contribution calculator starts at 0. number of articles start at 0
totalconnum=0;
totalcon=0;

// this function hides or shows the full ranking list	
function showRanking(x,fulllist)	{
	var id = x;
	if (fulllist==true) {	
	document.getElementById("table"+id).style.display="block";
	document.getElementById("btn"+id+"show").style.display="none";
	document.getElementById("btn"+id+"hide").style.display="inline";
	} else {
	document.getElementById("table"+id).style.display="none";	
			document.getElementById("btn"+id+"show").style.display="inline";
	document.getElementById("btn"+id+"hide").style.display="none";
	}
	
}
	
// this function shows the contribution of a specific user plus loads the table of full contributors	
const showContribution = async(x,y) => {

// where the information will be printed.
	var id=x;
	document.getElementById(id).innerHTML="loading..";
// update to new endpoint
	steem.api.setOptions({ url: 'https://api.steemit.com'});	

// retrieve article information
	const result = await steem.api.getContentAsync('<?=$author?>', y); 
	

// store pending payout value
	var payout = result.pending_payout_value;
	payout=parseFloat(payout.replace(" SBD", ""));

// store author payment
	var authorpay = result.total_payout_value;
	authorpay=parseFloat(authorpay.replace(" SBD",""));

// store curator payment
	var curatorpay = result.curator_payout_value;
	curatorpay=parseFloat(curatorpay.replace(" SBD",""));
	
// if article has already been paid out (pending payout is 0), then total payout is author + curator payments.
	if (payout<=0)
		{payout=authorpay+curatorpay;}
	
// retrieve voting information for article
	var activeVotes = result.active_votes;

// find length of voting information array
	var length=activeVotes.length;

// find out total amount of rshares in this article
	var total=0;
	for (x=0;x<length;x++) {		
		total=total+parseFloat(activeVotes[x]['rshares']);
	}

// sort users based on amount of rshares
	activeVotes.sort(function(a, b)
	{
		return b['rshares'] - a['rshares'];
	});
	
	console.log(activeVotes);
// print total payout to screen
    document.getElementById(id.toString()).innerHTML="<p>Article payout = $"+Math.round(payout*100)/100+"</p>";

// find ranking of user
	var rank=0;
    for (x=0;x<length;x++) {		
		if (activeVotes[x]['voter']=='<?=$voter?>') {
			rank=x+1;
		}
	}


// round contribution by user to 2d.p.
	var contribution = Math.round(parseFloat(activeVotes[rank-1]['rshares'])/total*payout*100)/100;
	
// fix NaN error in case total rshares = 0 
	
	if (isNaN(contribution)) {		
		contribution = 0;
	}

// print contribution to screen.
 	document.getElementById(id.toString()).innerHTML+="<p><?=$voter?> has contributed $"+contribution+" and is ranked at number "+rank+".</p>";
	
// add contribution to top of screen calculator	
	totalcon=totalcon+contribution;
	totalconnum++;
// show contribution on top of screen calculator.	
	document.getElementById("total_con").style.display="block";
	calculatorString='<h3>Calculator</h3> <p><a href="http://steemit.com/@<?=$voter?>"><b>@<?=$voter?></b></a> contributed a running total of<br>$'+Math.round(totalcon*100)/100+' from '+totalconnum;
	if (totalconnum>1) {
		calculatorString += ' articles to <a href="http://steemit.com/@<?=$author?>"><b>@<?=$author?></b></a>.</p>';
	} else {calculatorString+=' article to <a href="http://steemit.com/@<?=$author?>"><b>@<?=$author?></b></a>.</p>';}
	document.getElementById("total_con").innerHTML=calculatorString;
	
// print button to get full ranking list	
	document.getElementById(id.toString()).innerHTML+='<button class="btn btn-light" id="btn'+id+'show" onClick="showRanking('+id+',true)">Show Full Ranking List</button> ';
	
	document.getElementById(id.toString()).innerHTML+='<button class="btn btn-light" id="btn'+id+'hide" style="display:none" onClick="showRanking('+id+',false)">Hide Full Ranking List</button>';
	
	document.getElementById(id.toString()).innerHTML+="<br><br>";

// the ranktable is generated in string variable
	var rankTable ='<table id="table'+id+'" style="margin-left:0;display:none;"><tr><td>Rank</td><td>Username</td><td align="right">Amount Contributed</td><td align="right">Voting percentage</td></tr>';

// populate the ranktable
	for (x=0;x<length;x++) {
		rankTable+="<tr><td>";
		rank=x+1;
		rankTable+=rank;
		rankTable+="</td><td>";
		rankTable+='<a href="http://steemit.com/@'+activeVotes[x]['voter']+'">'+activeVotes[x]['voter']+'</a>';
		rankTable+="</td><td align='right'>$";		
		contribution=Math.round(parseFloat(activeVotes[x]['rshares'])/total*payout*100)/100;		
		rankTable+=contribution;
		rankTable+="</td><td align='right'>";
		rankTable+=activeVotes[x]['percent']/100+"%";
		rankTable+="</td></tr>";		
	}
	rankTable+="</table>";
// print the rank table
	document.getElementById(id.toString()).innerHTML+=rankTable;
	

};

// automate contribution calculation with async await function.
	
const outputData = async () => {
	
<?
for ($x=0;$x<sizeof($articleList);$x++) {
	echo "await showContribution($x, '".$articleList[$x]."');";	
}
	
?>	

}

// run contribution calculation function to automatically press all calculate contribution buttons.

outputData();

	
</script>



</html>