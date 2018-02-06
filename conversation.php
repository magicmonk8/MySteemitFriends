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

// connection to SteemSQL database. See https://github.com/Bulletproofmonk/PHPSteemSQL/blob/master/connectv7.php
include 'steemSQLconnect2.php';		

if ($_GET["User1"]&&$_GET["User2"]) {
	
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
     echo '<table class="table table-sm"><thead class="thead-inverse"><tr><th style="width:1%">Date</th><th style="width:1%">From</th><th style="width:1%">To</th><th>Comment Link</th></tr></thead><tbody>';
     
     // row number of $ button
     $rownum=0;
     
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td class='text-nowrap'>";
      echo $row[0];
      echo "</td><td>";
      echo $row[1];
      echo "</td><td>";
      echo $row[2];
	  echo "</td><td>";
      echo '<p><a href="http://steemit.com/@'.$row[1].'/'.$row[3].'">Link to Comment</a></p>';
	  echo '<div id='.$rownum.'><button type="button" class="btn btn-info" onClick="showArticle('.$rownum.',\''.$row[1].'\',\''.$row[3].'\')">Show Comment</button></div><br>';
	  $rownum++;
      echo "</td></tr>";
      
    }
      
  echo "</tbody></table>";

    unset($conn); unset($sth);
}
    
?>

</div>


</body>


<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

<script>
	

function showArticle(x,y,z) {

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
	
	steem.api.getContent(author,url, function(err,result) {
// print article text to correct location
		document.getElementById(id).innerHTML=result.body;
	});
	
}

</script>



	

</html>
