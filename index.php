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
    <style>
    	#bigtable {     background-color:#106288;   }    
    	@media only screen and (max-device-width: 480px) {  
    		#bigtable {     background-color:#106288;   } 
    	} 

    </style>
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
	<table style="background-color:#2E456D;border-collapse:collapse;border: 5px solid black;">
	  <tr>
	   <td class="text-center" style="padding:10px;">
		<h3>My Steemit Friends</h3><br>
		<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		  <div class="form-group">
			<label for="User">1. Type your Steemit UserName:</label><br>
			<input id="User" type="text" name="User" value="<? if ($_GET["User"]) { echo $_GET["User"];} ?>" autofocus>
		  </div>
		  2. Click the following buttons to see your:<br><br>
		  <div id="pleasescroll"></div>

		 <button id="upvotebtn" class="btn btn-primary" onclick="upBtnTxt()" type="submit">Upvote Stats</button><br>            
		</form>
		
		<div class="dropdown" id="rankingbtn">
		<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Ranking</button>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="#" onclick="loadRank('get_follower_rank')">Followers</a>
			<a class="dropdown-item" href="#" onclick="loadRank('get_esp_rank')">Effective SP</a>
			<a class="dropdown-item" href="#" onclick="loadRank('get_reputation_rank')">Reputation</a>			
		</div>	
		</div>
		<br>
		<button id="convbtn" class="btn btn-success" onclick="convBtn()">Conversation Record</button><br>     
		<br>		          

		<p>Created by <a href="http://steemit.com/@magicmonk"><img id="logo" src="images/magicmonkhead.png" width="64px">@magicmonk</a></p>
		<p><a href="https://steemit.com/steemit/@magicmonk/mysteemitfriend-s-new-edition-has-more-features-mysteemitfriends">Instructions</a></p>
	   </td>
	  </tr>
	</table>

<div id="ranking"></div>
   
<?php
// connect to SteemSQL database.
include 'steemSQLconnect2.php';		

// retrieve latest total vesting fund steem and total vesting shares for steem power calculations
$my_file = fopen("global.txt",'r');
$total_vesting_fund_steem=fgets($my_file);
$total_vesting_fund_steem = preg_replace('/[^0-9.]+/', '', $total_vesting_fund_steem);
$total_vesting_shares=fgets($my_file);
$total_vesting_shares = preg_replace('/[^0-9.]+/', '', $total_vesting_shares);
fclose($my_file);

// if a user name was provided in the URL, do the following	
if ($_GET["User"]) {
// hide other buttons while upvotes data is loading.
	echo '<script>
      document.getElementById("rankingbtn").style.display = "none";	  
	  document.getElementById("convbtn").style.display = "none";
	  document.getElementById("upvotebtn").innerHTML = "Loading..";
	  document.getElementById("logo").src="images/mmloading.gif";
		
	</script>';		
		
    $steemitUserName = rtrim(strtolower($_GET["User"]));
    $steemitUserName= str_replace("@", "", $steemitUserName);

// obtaining the following strings from the URL if they are available. If they are not available, set the defaults.	

// the default for the number of months is 3.
    if ($_GET["Months"]) {
    $months = $_GET["Months"];
    } else {
    $months = 3;
    }

// the default for included upvotes is 1: including comments. Other option - 2: articles only and no comments.
    if ($_GET["ArticlesOnly"]) {
    $articlesonly = $_GET["ArticlesOnly"];
    } else {
    $articlesonly = 1;
    }
    
    if ($_GET["Userfilter"]) {
    $userfilter = rtrim(strtolower($_GET["Userfilter"]));
    }

// the default for rank method is 1: number of votes only. Other options- 2: total weight. 3: SP*total weight.
    if ($_GET["RankMethod"]) {
    $rankmethod = $_GET["RankMethod"];
    } else {
    $rankmethod = 1;
    }                                  
    
     
if ($rankmethod==1) {    
  $sql = "SELECT voter, count(ID) FROM TxVotes (NOLOCK) WHERE (Author='$steemitUserName')"; 
  
  if ($months!=all) {
  $newdate = date("Y-m-d", strtotime("-".$months." months"));
  $sql = $sql." AND (timestamp>=Convert(datetime, '".$newdate."'))";
  }
  
  if ($userfilter) {
  $sql = $sql." AND voter LIKE '%".$userfilter."%'"; 
  }

  if ($articlesonly==2) {
  $sql = $sql." AND (permlink IN (SELECT permlink FROM Comments (NOLOCK) WHERE (Author = '$steemitUserName') AND (depth=0)))";  
  } 
  
  $sql = $sql." GROUP BY voter ORDER BY count(ID) DESC";    
     

   } else {

  $sql = "
SELECT voter, Vests, TW, Vests*TW As VTW
FROM (
SELECT a.voter, a.TW, convert(float, b.vesting_shares)-convert(float,b.delegated_vesting_shares)+convert(float,b.received_vesting_shares) AS Vests
FROM (SELECT voter, sum(weight)/100 as TW
FROM TxVotes (NOLOCK) 
WHERE (Author='$steemitUserName')
"; 
  if ($months!="all") {
   $newdate = date("Y-m-d", strtotime("-".$months." months"));
   $sql = $sql. "
AND (timestamp>=Convert(datetime, '".$newdate."'))
";}
  if ($userfilter) {
  $sql = $sql."
AND voter LIKE '%".$userfilter."%'
";}

   if ($articlesonly==2) {
   $sql = $sql."
AND (permlink IN (SELECT permlink FROM Comments (NOLOCK) WHERE (Author = '$steemitUserName') AND (depth=0)))
";}

$sql = $sql."
GROUP BY voter
) a LEFT JOIN (
SELECT name, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)) AS vesting_shares, Substring(delegated_vesting_shares,0,PATINDEX('%VESTS%',delegated_vesting_shares)) AS delegated_vesting_shares, Substring(received_vesting_shares,0,PATINDEX('%VESTS%',received_vesting_shares)) AS received_vesting_shares
FROM Accounts (NOLOCK)
) b on a.voter = b.name
) c";


  if ($rankmethod==2) {
    $sql=$sql." ORDER BY TW DESC";
    }
    
  if ($rankmethod==3) {
    $sql=$sql." ORDER BY VTW DESC";
  }         
}
    $sth = $conn->prepare($sql);
    $sth->execute();
    echo '<form method="get" action="';
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo '">';
    echo '
    <div class="row justify-content-center align-items-center" style="margin-top:1rem;">
    
    <div id="filterbox" class="col fbox" style="max-width: 400px;">
    <div class="card"><div class="card-body">';
    
    // print html code for filter for comments and articles 
    
    echo '
          <p>Votes include 
          <select name="ArticlesOnly">';
    if ($articlesonly==1) {echo '<option selected value="1">Articles and Comments</option>'; } else {echo '<option value="1">Articles and Comments</option>';}
    if ($articlesonly==2) {echo '<option selected value="2">Articles Only</option>'; } else {echo '<option value="2">Articles only</option>';}    
    echo '</select></p>
        
    In the last 
          <select name="Months">';
    if ($months==1) {echo '<option selected value="1">1</option>'; } else {echo '<option value="1">1</option>';}
    if ($months==2) {echo '<option selected value="2">2</option>'; } else {echo '<option value="2">2</option>';}
    if ($months==3) {echo '<option selected value="3">3</option>'; } else {echo '<option value="3">3</option>';}
    if ($months==4) {echo '<option selected value="4">4</option>'; } else {echo '<option value="4">4</option>';}
    if ($months==5) {echo '<option selected value="5">5</option>'; } else {echo '<option value="5">5</option>';}
    if ($months==6) {echo '<option selected value="6">6</option>'; } else {echo '<option value="6">6</option>';}
    if ($months=="all") {echo '<option selected value="all">all</option>'; } else {echo '<option value="all">all</option>';}
    echo '</select>
          months</p>
          
    <p>Ranked by <select name="RankMethod">';
    if ($rankmethod==1) {echo '<option selected value="1">Number of Votes</option>'; } else {echo '<option value="1">Number of Votes</option>';}
    if ($rankmethod==2) {echo '<option selected value="2">Total weight</option>'; } else {echo '<option value="2">Total weight</option>';}
    if ($rankmethod==3) {echo '<option selected value="3">SP x Total weight</option>'; } else {echo '<option value="3">SP x Total weight</option>';}     
    echo '</select></p>
    
    <p>Name contains <input type="text" id="Userfilter" name="Userfilter" placeholder="(Optional)" size="6" value="'.$userfilter.'"></p>
    
          <button class="btn btn-primary" type="submit">Update results</button>';
    echo '<input id="User" type="hidden" name="User" value="';
    echo $steemitUserName;
    echo '" style="display: none;visibility: hidden;">
    
    
    </div></div></div>
    
    </div>
    
    
  </form>';
    
   
         
        
    echo '<table id="bigtable" style="border-collapse:collapse;border: 5px solid black;"><tr><td style="vertical-align:top;background-color:#0f4880;border: 5px solid black;">';    

    echo '<p>Who has upvoted <a href="http://steemit.com/@'.$steemitUserName.'">'.$steemitUserName.'</a>?</p>'; 
    
// create table to display results       
    echo '<table class="table table-sm"><thead class="thead-inverse mobile"><tr><th>User</th>';
    
    if ($rankmethod==1) {echo '<th class="upvote" align="right"></th>';}
    if ($rankmethod==2) {echo '<th class="totalweight" align="right"></th>';}
    if ($rankmethod==3) {echo '<th class="sptotalweight" align="right"></th>';}
    echo '</tr></thead><tbody>';
    
// populate table with sql results (left hand side)
 
  if ($rankmethod==3) {   
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
    $voter=$row[0];
    $vests=$row[1];
    $totalweight=$row[2];
    $sp = $total_vesting_fund_steem * $vests / $total_vesting_shares;
    $sptw = $sp*$totalweight;
      echo '<tr><td class="mobile">';
      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$row[0].'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$voter.'\'>MSF Profile</a>">'.$voter.'</a>';
      echo "</td><td align='right'>";      
      echo '<a href="upvotelist.php?author='.$steemitUserName.'&voter='.$voter.'&Months='.$months.'&Articlesonly='.$articlesonly.'">'.number_format(round($sptw)).'</a>';
      echo "</td></tr>";
    }
  }
 
 
 if ($rankmethod==2) {   
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
    $voter=$row[0];
    $totalweight=$row[2];
      echo '<tr><td class="mobile">';
      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$voter.'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$voter.'\'>MSF Profile</a>">'.$voter.'</a>';
      echo "</td><td align='right'>";      
      echo '<a href="upvotelist.php?author='.$steemitUserName.'&voter='.$voter.'&Months='.$months.'&Articlesonly='.$articlesonly.'">'.number_format($totalweight).'</a>';
      echo "</td></tr>";
    }
  }
  
  
  
  
  if ($rankmethod==1) {
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
     echo '<tr><td class="mobile">';
      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$row[0].'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$row[0].'\'>MSF Profile</a>">'.$row[0].'</a>';
      echo "</td><td align='right'>";      
      echo '<a href="upvotelist.php?author='.$steemitUserName.'&voter='.$row[0].'&Months='.$months.'&Articlesonly='.$articlesonly.'">'.number_format($row[1]).'</a>';
      echo "</td></tr>";
    }
  }      
    echo "</tbody></table>";
    echo '</td><td style="vertical-align:top;background-color:#3a539b;">';
    echo '<p>Who has <a href="http://steemit.com/@'.$steemitUserName.'">'.$steemitUserName.'</a> upvoted?</p>';
    
    
    echo '<table class="table table-sm"><thead class="thead-inverse mobile"><tr><th>User</th>';
    if ($rankmethod==1) {echo '<th class="upvote" align="right"></th>';}
    if ($rankmethod==2) {echo '<th class="totalweight" align="right"></th>';}
    if ($rankmethod==3) {echo '<th class="sptotalweight" align="right"></th>';}
    echo '</tr></thead><tbody>';

if ($rankmethod==3) {
$sql = "
SELECT name, convert(float, b.vesting_shares)-convert(float,b.delegated_vesting_shares)+convert(float,b.received_vesting_shares) AS Vests
FROM (SELECT name, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)) AS vesting_shares, Substring(delegated_vesting_shares,0,PATINDEX('%VESTS%',delegated_vesting_shares)) AS delegated_vesting_shares, Substring(received_vesting_shares,0,PATINDEX('%VESTS%',received_vesting_shares)) AS received_vesting_shares
FROM Accounts (NOLOCK)
WHERE name='$steemitUserName'
) b ";
  $sth = $conn->prepare($sql);
    $sth->execute();
   while ($row = $sth->fetch(PDO::FETCH_NUM)) {
      $steemitUserPower = $row[1];
   }
   
   $steemitUserPower =$total_vesting_fund_steem * $steemitUserPower / $total_vesting_shares;
   
}
    
if ($rankmethod==1) {    
    if ($articlesonly==1) {
        
        if ($months!="all") {
          if ($userfilter) {
            $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND author LIKE '%".$userfilter."%' GROUP BY author ORDER BY count(ID) DESC";
          } else {
            $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) GROUP BY author ORDER BY count(ID) DESC";
          }
        } else {
          if ($userfilter) {
             $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND author LIKE '%".$userfilter."%' GROUP BY author ORDER BY count(ID) DESC"; 
          } else {
             $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' GROUP BY author ORDER BY count(ID) DESC"; 
          }
    
       } 
       
       } else {
       
        if ($months!="all") {
          if ($userfilter) {
            $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND author LIKE '%".$userfilter."%' AND (permlink IN (SELECT permlink FROM Comments WHERE author LIKE '%".$userfilter."%' AND depth=0 AND Created>=Convert(datetime, '".$newdate."'))) GROUP BY author ORDER BY count(ID) DESC";
          } else {
            $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND permlink NOT LIKE 're-%' GROUP BY author ORDER BY count(ID) DESC";
          }
        } else {
          if ($userfilter) {
             $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND author LIKE '%".$userfilter."%' AND (permlink IN (SELECT permlink FROM Comments WHERE depth=0 AND author LIKE '%".$userfilter."%')) GROUP BY author ORDER BY count(ID) DESC"; 
          } else {
             $sql = "SELECT author, count(ID) FROM TxVotes WHERE voter='$steemitUserName' AND permlink NOT LIKE 're-%' GROUP BY author ORDER BY count(ID) DESC"; 
          }
    
       } 
       
       
       }
} else {




    if ($articlesonly==1) {
        
        if ($months!="all") {
          if ($userfilter) {
            $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND author LIKE '%".$userfilter."%' GROUP BY author ORDER BY sum(weight) DESC";
          } else {
            $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) GROUP BY author ORDER BY sum(weight) DESC";
          }
        } else {
          if ($userfilter) {
             $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND author LIKE '%".$userfilter."%' GROUP BY author ORDER BY sum(weight) DESC"; 
          } else {
             $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' GROUP BY author ORDER BY sum(weight) DESC"; 
          }
    
       } 
       
       } else {
       
        if ($months!="all") {
          if ($userfilter) {
            $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND author LIKE '%".$userfilter."%' AND (permlink IN (SELECT permlink FROM Comments WHERE author LIKE '%".$userfilter."%' AND depth=0 AND Created>=Convert(datetime, '".$newdate."'))) GROUP BY author ORDER BY sum(weight) DESC";
          } else {
            $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND (timestamp>=Convert(datetime, '".$newdate."')) AND permlink NOT LIKE 're-%' GROUP BY author ORDER BY sum(weight) DESC";
          }
        } else {
          if ($userfilter) {
             $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND author LIKE '%".$userfilter."%' AND (permlink IN (SELECT permlink FROM Comments WHERE depth=0 AND author LIKE '%".$userfilter."%')) GROUP BY author ORDER BY sum(weight) DESC"; 
          } else {
             $sql = "SELECT author, sum(weight)/100 FROM TxVotes WHERE voter='$steemitUserName' AND permlink NOT LIKE 're-%' GROUP BY author ORDER BY sum(weight) DESC"; 
          }
    
       } 
       
       
       }




}   
    
    
    $sth = $conn->prepare($sql);
    $sth->execute();
    
    if ($rankmethod==3) {
    
    
     while ($row = $sth->fetch(PDO::FETCH_NUM)) {
     $totalweight=$row[1];
    $TWSP = $steemitUserPower * $totalweight; 
      echo '<tr><td class="mobile">';
      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$row[0].'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$row[0].'\'>MSF Profile</a>">'.$row[0].'</a>';
      echo "</td><td align='right'>";
      echo '<a href="upvotelist.php?author='.$row[0].'&voter='.$steemitUserName.'&Months='.$months.'&Articlesonly='.$articlesonly.'">'.number_format(round($TWSP)).'</a>';
      echo "</td></tr>";
         
    }
    
    } else {
    
    while ($row = $sth->fetch(PDO::FETCH_NUM)) {
    
      echo '<tr><td class="mobile">';
      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$row[0].'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$row[0].'\'>MSF Profile</a>">'.$row[0].'</a>';
      echo "</td><td align='right'>";
      echo '<a href="upvotelist.php?author='.$row[0].'&voter='.$steemitUserName.'&Months='.$months.'&Articlesonly='.$articlesonly.'">'.number_format($row[1]).'</a>';
      echo "</td></tr>";
         
    }
  }
	
		

	
 
    echo "</tbody></table>";  
    echo '</td></tr></table>';
	
	// show all buttons after upvotes data is displayed.
    echo '<script>
	      document.getElementById("pleasescroll").innerHTML = "<b><font color=yellow>Please <a href=\"#filterbox\">scroll down</a> to see the results.</font></b><br><br>";
		  document.getElementById("upvotebtn").innerHTML = "Upvote Stats";
		  document.getElementById("rankingbtn").style.display = "block";
		  document.getElementById("convbtn").style.display = "inline";
		  document.getElementById("logo").src="images/magicmonkhead.png";	
		  </script>';
		

     unset($conn); unset($sth);
    }
      ?>  
    </div>  
    </div>
    
       
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

// function to hide other buttons while upvote button is fetching data.
function upBtnTxt() {
	document.getElementById("upvotebtn").innerHTML = "Loading.."; 
	document.getElementById("rankingbtn").style.display = "none";
	document.getElementById("convbtn").style.display = "none";
	document.getElementById("logo").src="images/mmloading.gif";

}

// go to conversation record and load first user name
	  
function convBtn() {
	goToUser = document.getElementById("User").value;
	window.location.href = 'conversation.php?User1='+goToUser;
	
	
}
	  

// function to retrieve follower ranking	  
function loadRank(filename) {
	
	 var username;
  username =  document.getElementById("User").value;
  if (username=="") {alert ('Please enter a Steemit User Name!');return;}
  username = username.replace("@","");


  rankType = filename;

	document.getElementById("pleasescroll").innerHTML = "Loading..";
	document.getElementById("logo").src="images/mmloading.gif";
	document.getElementById("rankingbtn").style.display = "none";
	document.getElementById("upvotebtn").style.display = "none"; 
	document.getElementById("convbtn").style.display = "none";
  	document.getElementById("ranking").style.margin="auto";
	document.getElementById("ranking").style.marginTop="1.5rem";
	document.getElementById("ranking").style.marginBottom="1.5rem";
	document.getElementById("ranking").style.border="thick solid white";
	document.getElementById("ranking").style.maxWidth = "300px";
	document.getElementById("ranking").style.padding = "1rem";	
  	document.getElementById("ranking").innerHTML = "Loading..";
	

 
  
  var xhttp = new XMLHttpRequest();
	
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {		
		
		document.getElementById("logo").src="images/magicmonkhead.png";		
		document.getElementById("ranking").innerHTML = this.responseText;
		document.getElementById("pleasescroll").innerHTML = "<b><font color=#78EF15>Please <a href=\"#ranking\">scroll down</a> to see the ranking.</b><br><br>";
		document.getElementById("upvotebtn").style.display = "inline"; 
		document.getElementById("rankingbtn").style.display = "block";
		document.getElementById("convbtn").style.display = "inline";
		
    }

  };

  xhttp.open("GET", rankType+".php?SteemitUser=" + username, true);
  xhttp.send();

}


	  	  
    </script>
  </body>
</html>
