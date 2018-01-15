<html>

<head>
<title>My Steemit Friends</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<script src="jquery/jquery-3.2.1.min.js"></script>
<script src="popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<div class="container-fluid bg-1 text-center">

<div id="total_con" style="padding-top:1rem;padding-left:1rem;padding-right:1rem;border: 5px solid white; max-width:400px;margin:auto;display:none;margin-bottom:1rem;"></div>

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
      echo '<div id='.$rownum.'><button type="button" class="btn btn-info" onClick="showContribution('.$rownum.',\''.$row[1].'\')">Show Contribution Ranking & Add to Calculator</button></div><br>';
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

</body>

<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

<script>

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
	
function showContribution(x,y) {

	// where the information will be printed.
var id=x;
	document.getElementById(id).innerHTML="loading..";
	// update to new endpoint
steem.api.setOptions({ url: 'https://api.steemit.com'});	

// retrieve article information
steem.api.getContent('<?=$author?>', y ,function(err, result) {
	

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

// print contribution to screen.
 	document.getElementById(id.toString()).innerHTML+="<p><?=$voter?> has contributed $"+contribution+" and is ranked at number "+rank+".</p>";
	
// add contribution to top of screen calculator	
	totalcon=totalcon+contribution;
	totalconnum++;
// show contribution on top of screen calculator.	
	document.getElementById("total_con").style.display="block";
	calculatorString='<h3>Calculator</h3> <p><a href="http://steemit.com/@<?=$voter?>"><b><?=$voter?></b></a> contributed a running total of<br>$'+Math.round(totalcon*100)/100+' from '+totalconnum;
	if (totalconnum>1) {
		calculatorString += ' articles.</p>';
	} else {calculatorString+=' article.</p>';}
	document.getElementById("total_con").innerHTML=calculatorString;
	
// print button to get full ranking list
	
	document.getElementById(id.toString()).innerHTML+='<button class="btn btn-light" id="btn'+id+'show" onClick="showRanking('+id+',true)">Show Full Ranking List</button> ';
	
	document.getElementById(id.toString()).innerHTML+='<button class="btn btn-light" id="btn'+id+'hide" style="display:none" onClick="showRanking('+id+',false)">Hide Full Ranking List</button>';
	
	document.getElementById(id.toString()).innerHTML+="<br><br>";
	
	var rankTable ='<table id="table'+id+'" style="margin-left:0;display:none;"><tr><td>Rank</td><td>Username</td><td align="right">Amount Contributed</td><td align="right">Voting percentage</td></tr>';
	
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
	document.getElementById(id.toString()).innerHTML+=rankTable;


	
	

});

	
	
/*
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
*/
}



	
	
	
</script>



</html>
