<html>
<head>
	<title>My Steemit Friends</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="jquery/jquery-3.2.1.min.js"></script>
	<script src="popper.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css?3">
	<style>	
			.navbutton {
				width:10rem;
				margin:0.5rem;
			}	
	</style>
</head>
<body>
	
<!-- navbar --> 
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
    <a class="btn btn-lg btn-secondary navbutton nounderline"  href="articlelist.php">User History</a>
  </div> 
</nav>     
    
	
<!-- main page container -->
<div class="container-fluid bg-1 text-center">

<!-- form for obtaining user input -->
<form class="form-inline justify-content-center">	
	  <div class="form-group" style="margin-top:10px;">
		<label for="voter">User:&nbsp;</label>
		<input class="form-control" placeholder="Voter Username" id="voter" type="text" size="15" name="voter" value="<? if ($voter) {echo $voter;} ?>" autofocus>&nbsp;&nbsp;
	  </div>
	  <div class="form-group" style="margin-top:10px;">
	   <label for="fromDate">From Date:&nbsp;</label>  		
		<input class="form-control" name="date" id="date" type="date" value="<? if ($newdate) {echo $newdate;} else {echo $date;} ?>" id="date" min="2016-03-30" max="<?echo date("Y-m-d"); ?>">&nbsp;&nbsp;
	  </div>

	  <div class="form-group" style="margin-top:10px;">
	   <label for="toDate">To Date:&nbsp;</label>  		
		<input class="form-control" name="toDate" id="toDate" type="date" value="<? if ($todate) {echo $todate;} else {echo date("Y-m-d",strtotime("+1 day"));} ?>" id="toDate" min="2016-03-30" max="<?echo date("Y-m-d",strtotime("+1 day")); ?>">&nbsp;&nbsp;
	  </div>	  
</form>	
	  <button id="upvotebtn" class="btn btn-lg btn-primary" style="margin-top:10px;">List Articles Voted</button>&nbsp;&nbsp;
	  <button id="writtenbtn" class="btn btn-lg btn-primary" style="margin-top:10px;">List Articles Written</button><br><br>	

<?
	
// title at the top of page to state the voter and who is the author	
if ($results) {	 
	
	echo '<p><a href="http://steemit.com/@'.$voter.'"><b>@'.$voter.'</b></a> upvoted the following:</p>';

	// depending on whether the user clicked Voted or Written, display different table headings.
	if ($mode=='upvote') {
		$headings=array("Timestamp", "%", "Author", "Link");
	}
	
	if ($mode=='written') {
		$headings=array("Timestamp", "Author","Link");
	}

	// table begins here
	echo '<table class="table table-sm"><thead class="thead-inverse"><tr>';
	
	// generate table headings
	for ($x=0;$x<count($headings);$x++) {
		echo '<th>';
		echo $headings[$x];
		echo '</th>';		
	}
//	echo '<thead class="thead-inverse"><tr><th>'.$headings[0].'</th><th>'.$headings[1].'</th><th>'.$headings[2].'</th><th>'.$headings[3].'</th></tr>';

	// end of table headings, start of table body
	echo '</tr></thead><tbody>';

foreach ($results as $row) {
	echo "<tr>";
	echo "<td>";
	echo $row['timestamp'];
	echo "</td>";
	if ($mode=='upvote') {
		echo "<td>";
		echo $row['weight']/100;
		echo "</td>";
	}
	echo "<td>";
	echo $row['author'];
	echo "</td>";
	echo "<td>";
	echo '<p><a href="https://steemit.com/cn/@'.$row['author'].'/'.$row['permlink'].'" target="_top">'.$row['permlink'].'</a></p>';  
    echo "</td>";
	echo "</tr>";
	} 
}
	

echo "</tbody></table>";
	
?>

</div>
	
</body>

<!-- Javascript -->	
<script>
	
function retrieveInput() {
	goToUser = document.getElementById("voter").value;
	date = document.getElementById("date").value;
	toDate = document.getElementById("toDate").value;
}	
	
$(function(){

	$("#upvotebtn").click(
		  function() {	
			  retrieveInput();
			  window.location.href = 'articlelist.php?mode=upvote&voter='+goToUser+'&date='+date+'&toDate='+toDate;			  
		  }	  
	  );	
	
	$("#writtenbtn").click(
		  function() {	
			  retrieveInput();
			  window.location.href = 'articlelist.php?mode=written&voter='+goToUser+'&date='+date+'&toDate='+toDate;			  
		  }	  
	  );	
	
});
	
</script>	
	
</html>