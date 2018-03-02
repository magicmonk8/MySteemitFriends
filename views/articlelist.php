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
    <a class="btn btn-lg btn-secondary navbutton nounderline"  href="articlelist.php">Vote History</a>
  </div> 
</nav>     
    

<div class="container-fluid bg-1 text-center">

<form class="form-inline justify-content-center" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="">
		  <div class="form-group" style="margin-top:10px;">
			<label for="voter">Voter:&nbsp;</label>
			<input class="form-control" placeholder="Voter Username" id="voter" type="text" size="15" name="voter" value="<? if ($voter) {echo $voter;} ?>" autofocus>&nbsp;&nbsp;
	      </div>
		  <div class="form-group" style="margin-top:10px;">
 		   <label for="fromDate">From Date:&nbsp;</label>  		
    		<input class="form-control" name="date" type="date" value="<? if ($newdate) {echo $newdate;} else {echo $date;} ?>" id="date" min="2016-03-30" max="<?echo date("Y-m-d"); ?>">&nbsp;&nbsp;
  		   </div>
  		  
  		   <div class="form-group" style="margin-top:10px;">
 		   <label for="toDate">To Date:&nbsp;</label>  		
    		<input class="form-control" name="toDate" type="date" value="<? if ($todate) {echo $todate;} elseif ($months=='all') {echo '2016-03-30';} else {echo date("Y-m-d");} ?>" id="toDate" min="2016-03-30" max="<?echo date("Y-m-d"); ?>">&nbsp;&nbsp;
  		   </div>
   
		 <button id="upvotebtn" class="btn btn-lg btn-primary" type="submit" style="margin-top:10px;">List Articles</button><br>            
		</form>	

<?
	
// title at the top of page to state the voter and who is the author	
if ($results) {

echo '<p><a href="http://steemit.com/@'.$voter.'"><b>@'.$voter.'</b></a> upvoted the following:</p>';

echo '<table class="table table-sm"><thead class="thead-inverse"><tr><th>Timestamp</th><th>%</th><th>Author</th><th>Link</th></tr></thead><tbody>';


foreach ($results as $row) {
	 echo "<tr><td>";
      echo $row['timestamp'];
      echo "</td><td>";
      echo $row['weight']/100;
      echo "</td><td>";
      echo $row['author'];
      echo "</td><td>";
      echo '<p><a href="https://steemit.com/cn/@'.$row['author'].'/'.$row['permlink'].'" target="_top">'.$row['permlink'].'</a></p>';  
      echo "</td></tr>";
}
	
}
echo "</tbody></table>";
	
?>

</div>

</body>

</html>