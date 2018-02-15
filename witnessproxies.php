<html>
  <head>
    <title>Witness Proxies Voting Power Ranking - My Steemit Friends</title>
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
		background-color:#052715;
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

    
<nav id="mynav" class="navbar navbar-expand-sm navbar-dark">
  <span class="navbar-brand mb-0 h1"><a href="http://steemit.com/@magicmonk"><img src="images/magicmonkhead.png" width="64px">@magicmonk</a></span>

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
    </div>
  </div><!-- /btn-group -->
    <a class="btn btn-lg btn-danger navbutton nounderline"  href="upvotelist.php">$ Calculator</a>
</nav>     
      
   
    <div class="container-fluid bg-4 text-center" style="max-width:1000px;">

    <div class="row">

     <div class="col">

     

<h1>Steemit Witness Proxies Voting Power Ranking</h1>       

<br>

<div style="border:5px solid white;padding:10px;">

<h3>Search Username for Ranking</h3>



<form>Steemit UserName: <input id="username" type="text" size="15"></form>

<button type="button" onclick="loadDoc()">Show Ranking (wait a few seconds)</button><br><br>

<div id="ranking"></div>

</div>

<br><br>

<div style="border:5px solid white;padding:10px;">

<h3>Select page number</h3><br>



<?php

// number formatting required for vests
function custom_number_format($n, $precision = 3) {   
        $n_format = number_format($n / 1000000, $precision);
    
    

    return $n_format;
}	
	
// connect to SteemSQL database
include 'steemSQLconnect2.php';		

	
// number of pages on the browsing panel
$numberofpages=7;

$pagesize=50;

if ($_GET["page"]) {

// sanitize input with filter_var, make sure the input is an integer.
$page = filter_var($_GET["page"],FILTER_VALIDATE_INT);

} else {$page=1;}





if ($_GET["highlight"]) {

$highlight = strtolower($_GET["highlight"]);

}









// where to start the results

$offset = ($page - 1) * $pagesize;





echo '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';



if ($page>=4) {

for ($x=$page-3;$x<=$page+3;$x++) {      

      if ($x==$page) {

        echo '<li class="page-item active"><a class="page-link" href="witnessproxies.php?page='.$x.'">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="witnessproxies.php?page='.$x.'">'.$x.'</a></li>';

      }  

  }

}  else {

  for ($x=1;$x<=$numberofpages;$x++) {      

      if ($x==$page) {

        echo '<li class="page-item active"><a class="page-link" href="witnessproxies.php?page='.$x.'">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="witnessproxies.php?page='.$x.'">'.$x.'</a></li>';

      }  

  }    

} 

echo '</ul></nav><br>';

if ($page>1) {

echo '<a href="witnessproxies.php?page='.($page-1).'" class="btn btn-light" role="button">Previous Page</a> ';

}

echo '<a href="witnessproxies.php?page='.($page+1).'" class="btn btn-light" role="button">Next Page</a><br><br>'; 



echo '<form action="witnessproxies.php" method="get">Go To Page Number <input type="text" name="page" size="5"> <input type="submit" value="Go"></form><br></div>';


    $sql = "
;With q1 as
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
),

q2 as
(
SELECT name, witness_votes
FROM Accounts
)
select q1.*, q2.witness_votes AS witness_votes
from q1 INNER JOIN q2
ON q1.proxy=q2.name
ORDER BY q1.total_vests DESC
OFFSET :offset ROWS
FETCH NEXT :pagesize ROWS ONLY;
	";

    


// prepare the SQL statement, then bind value to variables, this prevents SQL injection.
    $sth = $conn->prepare($sql);
    $sth -> bindValue(':offset', $offset, PDO::PARAM_INT);
 	$sth -> bindValue(':pagesize', $pagesize, PDO::PARAM_INT);

	
    $sth->execute();

    echo '</div><div class="col">';

echo '<table id="bigtable" class="table table-sm table-striped" style="background-color:#0f4880;border:5px solid white">';

    

echo '<thead class="thead-default mobile"><tr><th style="text-align: center;">Ranking</th><th style="text-align: center;">Proxy</th><th style="text-align: center;">Total Vests (millions)</th><th style="text-align: center;">Own Vests (millions)</th><th style="text-align: center;">Proxied Vests (millions)</th><th style="text-align: center;">Votes casted</th></tr></thead>';

    // print the results. If successful, magicmonk will be printed on page.

    $rank=$pagesize*($page-1)+1;
    $rownum=0;
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 

// convert vests to sp		

		$name=$row['proxy'];
		$totalvests=$row['total_vests'];
		$ownvests=$row['own_vests'];
		$proxiedvests=$row['proxiedvests'];
		$witnessvotes=json_decode($row['witness_votes']);
		
// calculation of SP formula no longer used (done in SQL). Kept here for reference: $ownsp = $total_vesting_fund_steem * $ownvests / $total_vesting_shares;
		
// create striped rows		
	  if ($rownum%2==0) {echo '<tr>';} else {echo '<tr style="background-color:#0f3066">';}
		$rownum++;
      echo '<td style="text-align: center;">';

      echo $rank;

      $rank++;

      echo "</td><td>";

      if ($name==$highlight) {

      echo '<span style="background-color:red">';

      } 

      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$name.'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$name.'\'>MSF Profile</a>">'.$name.'</a>';

      if ($name==$highlight) {

      echo '</span>';

      } 

      echo "</td><td class='alignright'>";		

	  echo custom_number_format($totalvests);


      echo "</td><td class='alignright'>";

      echo custom_number_format($ownvests);  


	  echo "</td><td class='alignright'>";
		
// link to steemdb.com to show who has set proxies to this user & their vests.				
      echo '<a href="https://steemdb.com/@'.$name.'/proxied">';
	  echo custom_number_format($proxiedvests);
      echo '</a>';		
		
	  echo "</td><td class='alignright'>";

// show in popover who this user has voted for.		
		
		
echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="';
	foreach($witnessvotes as $value) {
    echo "<font color='white'>".$value."</font><br>";
    }
	echo '">'.count($witnessvotes).'</a>';		
		    
      echo "</td></tr>";
    
	  }

echo "</table>";



// terminate connectiion

unset($conn); unset($sth);

  

?>  



</div>

</div>

</div>

</body>



<script>



$(function(){

    // Enables popover with html content

    $("[data-toggle=popover]").popover(

    {

    html:true

    }

    );

  

  

  // code for making sure only 1 pop over is open, the others close.

  

  $("[data-toggle=popover]").on('click', function (e) {

        $("[data-toggle=popover]").not(this).popover('hide');

    });

  

  

    

});





function loadDoc() {

  document.getElementById("ranking").innerHTML = "Loading..";

  var username;

  username =  document.getElementById("username").value;

  username = username.replace("@","");

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      document.getElementById("ranking").innerHTML = this.responseText;

    }

  };

  xhttp.open("GET", "get_proxy_rank.php?SteemitUser=" + username, true);

  xhttp.send();

}

</script>







</html>
