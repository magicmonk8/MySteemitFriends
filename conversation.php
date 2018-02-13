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
			margin:1rem;
		}
</style>

</head>

<body>

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
    
           
    

<div class="container-fluid bg-1 text-center" style="padding-top:20px;">

<form class="form-inline" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		  <div class="form-group" style="margin-top:10px;">
			<label for="User1">Username 1:&nbsp;</label>
			<input class="form-control" placeholder="Enter UserName" id="User1" type="text" name="User1" value="<? if ($_GET["User1"]) { echo $_GET["User1"];} ?>" <? if (!$_GET["User1"]) { echo 'autofocus';} ?>>&nbsp;&nbsp;
	      </div>
	      <div class="form-group" style="margin-top:10px;">
			<label for="User2">Username 2:&nbsp;</label>
			<input class="form-control" placeholder="Enter Another UserName" id="User2" type="text" name="User2" value="<? if ($_GET["User2"]) { echo $_GET["User2"];} ?>" <? if ($_GET["User1"]) { echo 'autofocus';} ?>>&nbsp;&nbsp;
		  </div>
		 <br>
		 <button id="upvotebtn" class="btn btn-lg btn-primary" type="submit" style="margin-top:10px;">See conversation record</button><br>            
		</form>
		
<?php


// only execute code if information has been submitted.
if ($_GET["User1"]&&$_GET["User2"]) {

// connection to SteemSQL database. See https://github.com/Bulletproofmonk/PHPSteemSQL/blob/master/connectv7.php
include 'steemSQLconnect2.php';		

// list of article addresses for displaying comments later.	
$articleList=array();

// list of authors for each comment.
$authorList=array();

// start putting addresses in the list at 0 element
$articleIndex=0;

// start putting authors in the list at 0 elemnt
$authorIndex=0;
	
	
// steemit user details
$user1 = rtrim(strtolower($_GET["User1"]));
$user1= str_replace("@", "", $user1);	
$user2 = rtrim(strtolower($_GET["User2"]));
$user2= str_replace("@", "", $user2);		

	
$sql = "
SELECT created, author as 'From', parent_author as 'To', permlink 
FROM Comments 
WHERE (Author = '".$user1."' AND parent_author='".$user2."') OR (author='".$user2."' AND parent_author='".$user1."')
ORDER BY created DESC
";
    

    
// prepares the SQL statement to be executed.    
$sth = $conn->prepare($sql);

// execute SQL statement.	
$sth->execute();    
     echo '<table class="table table-sm"><thead class="thead-inverse"><tr><th style="width:1%">Timestamp</th><th style="width:1%">From</th><th style="width:1%">To</th><th>Comment Link</th></tr></thead><tbody>';
     
     // row number of $ button
     $rownum=0;
     
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td>";
	// code for reformatting time
	$s = $row[0];
	$dt = new DateTime($s);

	$date = $dt->format('m/d/Y');
	$time = $dt->format('H:i:s');

	  echo $date."<br>".$time;
    
      echo "</td><td>";
      echo $row[1];
      echo "</td><td>";
      echo $row[2];
	  echo "</td><td>";
      echo '<p><a href="http://steemit.com/@'.$row[1].'/'.$row[3].'">Link to Comment</a></p>';

// store URL of articles in articleList array
 $articleList[$articleIndex]=$row[3];
 $articleIndex++;

// store list of authors in authorList array
$authorList[$authorIndex]=$row[1];
$authorIndex++;

	  echo '<div id='.$rownum.'></div><br>';
	  $rownum++;
      echo "</td></tr>";
      
    }

if ($rownum==0) {echo "No Results Found! Check the spelling of Usernames.<br><br>";}
      
  echo "</tbody></table>";

    unset($conn); unset($sth);
}
    
?>

</div>


</body>


<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

<script>
	

const showArticle = async(x,y,z) => {

// article url
	var url=z;
	
// who is the author
	var author=y;
	
// where comment text will be printed
	var id=x;
	document.getElementById(id).innerHTML="loading..";

// update settings to new endpoint
	steem.api.setOptions({ url: 'https://api.steemit.com'});

// retrieve article text
	
const result = await steem.api.getContentAsync(author,url);
 
// print article text to correct location
		document.getElementById(id).innerHTML=result.body;
	
};

const outputData = async () => {
	
<?
for ($x=0;$x<sizeof($articleList);$x++) {
	echo "await showArticle($x, '".$authorList[$x]."','".$articleList[$x]."');";	
}
	
?>	
}

outputData();



</script>



	

</html>