<html>
  <head>
    <title>Power Down Ranking - My Steemit Friends</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="jquery/jquery-3.2.1.min.js"></script>
    <script src="extensions/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css?7">
    <style>
  
	/*background color */
	.bg {
		background-color:#2b1d3a;
		color: white;
	}	
	
	/*enlarge radio button */
	input[type=radio] {
    border: 0px;
    width: 1.3em;
    height: 1.3em;
	vertical-align:middle;
	}

		
	input[type=number]{
    width: 3.5rem;
    } 

		
    </style>

  </head>

  <body class="bg">   

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
   		<a class="dropdown-item" href="powerdown.php">Power Down</a> 
   		<a class="dropdown-item" href="witnessvoting.php">Witness Voting Power: All Users</a>          
   		<a class="dropdown-item" href="witnessproxies.php">Witness Voting Power: Proxies</a> 
    </div>
  </div><!-- /btn-group -->
    <a class="btn btn-lg btn-danger navbutton nounderline"  href="upvotelist.php">$ Calculator</a>
</nav>     
                
   
    <div class="container-fluid bg text-center" style="max-width:1000px;">

    <div class="row">

     <div class="col">


<h1>Steemit Power Down Ranking</h1>       

<br>

<b><font size="+2" color=#78EF15>Please <a href="#rtable">scroll down</a> to see the table.</b></font><br><br>


<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Search Username for Ranking</h3>



<form>Steemit UserName: <input id="username" type="text" size="15"></form>

<button type="button" onclick="loadDoc()">Show Ranking (wait a few seconds)</button><br><br>

<div id="ranking"></div>

</div>

<br><br>

<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Sum of SP being withdrawn</h3>


<form>From the top <input name="topSPNum" id="topSPNum" type="number" min="1" max="500" value="200"> SP holders<br>
(Maximum 500)
</form>

<button type="button" onclick="calcSumSP()">Calculate</button><br><br>

<div id="sumSP"></div>

</div>

<br><br>

<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Options</h3>
<?php 
	if ($_GET["rankopt"]) { 
		$rankopt = $_GET["rankopt"];
	} else {$rankopt='allusers';}
?>
<form method="get" action="<?php filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_FULL_SPECIAL_CHARS); ?>">
<span style="font-size:1.5rem">Ranking table includes:</span><br><br>

 <input type="radio" name="rankopt" value="allusers" <? if ($rankopt=='allusers') {echo "checked";}?>> <span style="font-size:1.3rem">All Users</span><br>
  <input type="radio" name="rankopt" value="pdonly" <? if ($rankopt=='pdonly') {echo "checked";}?>> <span style="font-size:1.3rem">Powering Down Users</span><br><br>
 
	<button type="submit" class="btn btn-default">Refresh</button>

</form>

</div>

<br><br>

<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';		

	
// retrieve Coin Market Cap steem price in USD for calculating withdrawl amount
$my_file = fopen("CMCsteemprice.txt",'r');
$steemprice=fgets($my_file);
$steemprice= preg_replace('/[^0-9.]+/', '', $steemprice);
fclose($my_file);

?>

<div style="border:5px solid white;padding:10px;">
<h3>Explanation</h3>
<p><b>Withdrawl Amount (SP):</b> How much SP is withdrawn at the next power down.</p> 
<p><b>Withdrawl Amount (USD):</b> The equivalent value of the SP withdraw in USD according to Coinmarketcap latest prices (<b>1 Steem = <? echo round($steemprice,2);?> USD.</b></p> 
<p><b>Next Withdrawl Date:</b> When the user will be withdrawing this amount.</p> 
<p><b>Current SP:</b> How much Steem Power the user currently has.</p>
<p><b>Power Down Start Date:</b> When the user started powering down (only the most recent power down can be tracked).</p>
<p><b>Power Down Duration:</b> How long the user has been powering down.</p>

<p><b>N/A:</b> Not Applicable. If a user is not powering down, the table will not display the power down dates.</p>


</div>	
<br><br>


<div style="border:5px solid white;padding:10px;max-width:500px;margin:auto">

<h3>Select page number</h3><br>



<?
	
// retrieve current steem median history price for calculating account value
$my_file = fopen("global.txt",'r');
$total_vesting_fund_steem=fgets($my_file);
$total_vesting_fund_steem = preg_replace('/[^0-9.]+/', '', $total_vesting_fund_steem);
$total_vesting_shares=fgets($my_file);
$total_vesting_shares = preg_replace('/[^0-9.]+/', '', $total_vesting_shares);
fclose($my_file);


// amount of steem per vest (needed to convert vests to steem)
	
$steem_per_vest = round($total_vesting_fund_steem / $total_vesting_shares, 6, PHP_ROUND_HALF_UP);
echo $steem_per_vest;
	
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

        echo '<li class="page-item active"><a class="page-link" href="powerdown.php?page='.$x.'';
		
		if ($rankopt=='allusers') {
			echo '&rankopt=allusers';
		}
		  
		  echo '">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="powerdown.php?page='.$x.'';
		  
		if ($rankopt=='allusers') {
			echo '&rankopt=allusers';
		}		  
		  
		 echo '">'.$x.'</a></li>';

      }  

  }

}  else {

  for ($x=1;$x<=$numberofpages;$x++) {      

      if ($x==$page) {

        echo '<li class="page-item active"><a class="page-link" href="powerdown.php?page='.$x.'';
		
		if ($rankopt=='allusers') {
			echo '&rankopt=allusers';
		}
		  
		  
		  echo '">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="powerdown.php?page='.$x.'';
		  
				if ($rankopt=='allusers') {
			echo '&rankopt=allusers';
		}  
		  
		  echo '">'.$x.'</a></li>';

      }  

  }    

} 

echo '</ul></nav><br>';

if ($page>1) {

echo '<a href="powerdown.php?page='.($page-1).'';

if ($rankopt=='allusers') {
	echo '&rankopt=allusers';
}	
	
	
echo '" class="btn btn-light" role="button">Previous Page</a> ';

}

echo '<a href="powerdown.php?page='.($page+1).'';

if ($rankopt=='allusers') {
	echo '&rankopt=allusers';
}

	
echo '" class="btn btn-light" role="button">Next Page</a><br><br>'; 



echo '<form action="powerdown.php" method="get">Go To Page Number <input type="text" name="page" size="5"> <input type="submit" value="Go">';

	   echo '<input type="hidden" name="rankopt" value="';
    echo $rankopt;
    echo '" style="display: none;visibility: hidden;">';
	
	
	echo '</form><br></div>';
	
echo '<br><br>';	

echo '</div><div class="col">';

	
if ($rankopt=='pdonly') {
	// SQL for ranking table containing users only powering down
	$sql = "
SELECT name, convert(float, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)))*".$steem_per_vest." AS sp, 
convert(float, Substring(vesting_withdraw_rate,0,PATINDEX('%VESTS%',vesting_withdraw_rate)))*".$steem_per_vest." AS next_withdrawl_sp, 
next_vesting_withdrawal, 
b.maxtime as withdrawl_start_date
FROM Accounts (NOLOCK) a INNER JOIN
(select account, max(timestamp) AS maxtime
from TxWithdraws (NOLOCK)
group by account
) b
ON a.name = b.account

where vesting_withdraw_rate NOT LIKE '0.0000%'
Order by next_withdrawl_sp DESC
OFFSET :offset ROWS
FETCH NEXT :pagesize ROWS ONLY;
	
	";

} else {
	// SQL for ranking table containing all users
		$sql = "
SELECT name, convert(float, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)))*".$steem_per_vest." AS sp, 
convert(float, Substring(vesting_withdraw_rate,0,PATINDEX('%VESTS%',vesting_withdraw_rate)))*".$steem_per_vest." AS next_withdrawl_sp, 
next_vesting_withdrawal, 
b.maxtime as withdrawl_start_date
FROM Accounts (NOLOCK) a LEFT JOIN
(select account, max(timestamp) AS maxtime
from TxWithdraws (NOLOCK)
group by account
) b
ON a.name = b.account
Order by sp DESC
OFFSET :offset ROWS
FETCH NEXT :pagesize ROWS ONLY;
	
	";
	
	
}
	
// prepare the SQL statement, then bind value to variables, this prevents SQL injection.
    $sth = $conn->prepare($sql);
    $sth -> bindValue(':offset', $offset, PDO::PARAM_INT);
 	$sth -> bindValue(':pagesize', $pagesize, PDO::PARAM_INT);

	
    $sth->execute();



echo '<table id="bigtable" class="table table-sm table-striped" style="background-color:#0f4880;border:5px solid white">';

    

echo '<thead class="thead-default mobile"><tr><th style="text-align: center;">R<br>a<br>n<br>k</th><th>User Name</th><th style="text-align: center;">Withdrawl Amount (SP)</th><th style="text-align: center;">Withdrawl Amount (USD)</th><th style="text-align: center;">Next Withdrawl Date</th><th style="text-align: center;">Current SP</th><th style="text-align: center;">Power Down Start Date</th><th style="text-align: center;">Power Down Duration</th></tr></thead>';

    // print the results. If successful, magicmonk will be printed on page.

    $rank=$pagesize*($page-1)+1;
    $rownum=0;
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 

// convert vests to sp		

		$name=$row['name'];
		$sp=$row['sp'];
		$next_withdrawl_sp=$row['next_withdrawl_sp'];
		$next_withdrawl_usd=$next_withdrawl_sp*$steemprice;
		$next_withdrawl_date=$row['next_vesting_withdrawal'];
		$withdrawl_start_date=$row['withdrawl_start_date'];	

		
// calculation of SP formula no longer used (done in SQL). Kept here for reference: $ownsp = $total_vesting_fund_steem * $ownvests / $total_vesting_shares;
		
// highlight power down rows
	  if ($next_withdrawl_sp<=0.01) {echo '<tr>';} else {echo '<tr style="background-color:#73200F">';}
		
      echo '<td style="text-align: center;">';

      echo $rank;

      $rank++;

      echo "</td><td>";

      if ($name==$highlight) {

      echo '<span style="background-color:black">';

      } 

      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$name.'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$name.'\'>MSF Profile</a>">'.$name.'</a>';

      if ($name==$highlight) {

      echo '</span>';

      }          
		
		echo "</td><td class='alignright'>";
		
		echo number_format(round($next_withdrawl_sp)); 
		
		echo "</td><td class='alignright'>";
		
		echo "$".number_format(round($next_withdrawl_usd)); 		
		
		
		echo "</td><td style='text-align: center;' class='text-nowrap'>";
		
		// convert timestamp to date
		
		$dt1 = new DateTime($next_withdrawl_date);
		$date1 = $dt1->format('Y-m-d');				
		
		
		if ($next_withdrawl_sp<=0.01) {			
		    // if not powering down, print N/A (Not Applicable).
			echo "N/A";
		} else {
			
			echo $date1;	
		}
		
		
		echo "</td><td class='alignright'>";
		
		echo number_format(round($sp));  
          
		echo "</td><td style='text-align: center;' class='text-nowrap'>";

		$dt2 = new DateTime($withdrawl_start_date);
		$date2 = $dt2->format('Y-m-d');		
		
		// only print date if powering down
		if ($next_withdrawl_sp<=0.01) {	
			// if not powering down, print N/A (Not Applicable).
			echo "N/A";
				    
	
		} else {
			echo $date2;
		}
		
	
          
		echo "</td><td style='text-align: center;' class='text-nowrap'>";
// find difference between 2 dates tutorial: http://php.net/manual/en/datetime.diff.php
// only print time if powering down.		
		if ($next_withdrawl_sp<=0.01) {	
			
        echo "N/A";
			
		} else {
		
			$interval = date_diff($dt2, $dt1);
		$days=$interval->format('%a');
		$weeks=floor($days/7);
		$leftdays=$days%7;
		echo $weeks." weeks<br>and ".$leftdays." days";	
		}
		
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

<?
if ($rankopt=='allusers') {
	echo 'xhttp.open("GET", "get_powerdown_rank.php?rankopt=allusers&SteemitUser=" + username, true);';
} else {
	echo 'xhttp.open("GET", "get_powerdown_rank.php?rankopt=pdonly&SteemitUser=" + username, true);';
	
}
	
?>
  

  xhttp.send();

}
	
	
	function calcSumSP() {

  document.getElementById("sumSP").innerHTML = "Loading..";

  var topSPNum;

  topSPNum =  document.getElementById("topSPNum").value;  

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      document.getElementById("sumSP").innerHTML = this.responseText;

    }

  };

  xhttp.open("GET", "calcSumSP.php?&topSPNum=" + topSPNum, true);

  xhttp.send();

}

</script>







</html>