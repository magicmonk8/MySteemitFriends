<html>
  <head>
    <title>
<?
  if ($mode=="sbd") {
    $title="SBD";
    $bgcolor="#2E1414";
    $ajaxfile="get_sbd_rank.php";
    $columnheadings=array("SBD","Own SP","EffectiveSP");
  } elseif ($mode=="ownSP") {
    $title="Own SP";
    $bgcolor="#44174A";
    $ajaxfile="get_osp_rank.php";
    $columnheadings=array("Own SP","EffectiveSP", "SBD");
  } elseif ($mode=="accountCreation") {
    $title="Account Creation Date";
    $bgcolor="#1E716E";
    $columnheadings=array("CreationDate","Mined","Own SP");
    $ajaxfile="get_creation_rank.php";
  }                      
  echo $title;
?>
     Ranking - Steem Friends
    </title>
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
  		  background-color:<?echo $bgcolor;?>;
  		  color: white;
      }
    </style>
  </head>
  <body class="bg-4">  
    <? include 'views/navbar.php'; ?>    
    <div class="container-fluid bg-4 text-center" style="max-width:1000px;">  
      <div class="row">  
        <div class="col">     
          <h1>Steemit <?echo $title;?> Ranking</h1>
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
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center">
<?php   
  // number of pages on the browsing panel
  $numberofpages=7;
  // which page numbers appear on the page selection panel? Only 7 numbers will appear.
  if ($page>=4) {  
    for ($x=$page-3;$x<=$page+3;$x++) { 
      if ($x==$page) {
        echo '<li class="page-item active"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.$x.'">'.$x.'</a></li>';          
      } else {
        echo '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.$x.'">'.$x.'</a></li>';
      } 
    }  
  } else {
    for ($x=1;$x<=$numberofpages;$x++) {      
      if ($x==$page) {
        echo '<li class="page-item active"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.$x.'">'.$x.'</a></li>';  
        } else {  
        echo '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.$x.'">'.$x.'</a></li>';  
        }  
    } 
  }
?>  
              </ul>
            </nav><br>  
<?  
  if ($page>1) {  
    echo '<a href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.($page-1).'" class="btn btn-light" role="button">Previous Page</a> ';  
  }  
  echo '<a href="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'&page='.($page+1).'" class="btn btn-light" role="button">Next Page</a><br><br>';
?>                      
              Go To Page Number 
              <input type="text" id="goToPage" size="5">
              <a id="goToBtn" class="btn btn-primary" onclick="goToBtn()">Go</a>            
            <br>
          </div>
        </div>
        <div class="col">
          <table id="bigtable" class="table table-sm table-striped" style="background-color:#0f4880;border:5px solid white">  
            <thead class="thead-default mobile">
              <tr>
                <th style="text-align: center;">Ranking</th>
                <th>User Name</th>
                <th class="alignright"><?=$columnheadings[0]?></th>
                <th class="alignright"><?=$columnheadings[1]?></th>
                <th class="alignright"><?=$columnheadings[2]?></th>
              </tr>
            </thead>
            <tbody>
<?
  // print the results.
  $rank=$pagesize*($page-1)+1;
  $rownum=0;
  while ($row = $results->fetch(PDO::FETCH_NUM)) {  
    // convert vests to sp		
    if ($mode=="sbd" or $mode=="ownSP") {
    	$vests=$row[0];
    	$sp = $total_vesting_fund_steem * $vests / $total_vesting_shares;
    	$ownvests=$row[2];
    	$ownsp = $total_vesting_fund_steem * $ownvests / $total_vesting_shares;
      $user=$row[1];
    } elseif ($mode=="accountCreation") {
     	$ownvests=$row[1];
    	$ownsp = $total_vesting_fund_steem * $ownvests / $total_vesting_shares;
      $user=$row[0];    
    }
    // create striped rows		
  	if ($rownum%2==0) {echo '<tr>';} else {echo '<tr style="background-color:#0f3066">';}
  	$rownum++;
  	echo '<td style="text-align: center;">';
    echo $rank;
    $rank++;
    echo "</td><td>";
    if ($user==$highlight) {
      echo '<span style="background-color:red">';
    }
    echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$user.'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$user.'\'>MSF Profile</a>">'.$user.'</a>';
    if ($user==$highlight) {
      echo '</span>';
    } 
    echo "</td>";
    // depending on the type of ranking, display different columns
    if ($mode=="sbd") {
      echo "<td class='alignright'>";
  	  echo number_format(round($row[3]));
  	  echo "</td><td class='alignright'>";
      echo number_format(round($ownsp));  
      echo "</td><td class='alignright'>";
      echo number_format(round($sp));
      echo "</td></tr>";
   
    } elseif ($mode=="ownSP") {
      echo "<td class='alignright'>";
      echo number_format(round($ownsp));  
      echo "</td><td class='alignright'>";
      echo number_format(round($sp));
      echo "</td><td class='alignright'>";
      echo number_format(round($row[3]));
      echo "</td></tr>";
    } elseif ($mode=="accountCreation") {
      echo "<td class='alignright'>";
      $s = $row[2];
	    $dt = new DateTime($s);
	    $date = $dt->format('m/d/Y');     
      echo $date;
      echo "</td><td class='alignright'>";
      if ($row[3]==1) {echo "true";} else {echo "false";}
      echo "</td><td class='alignright'>";
      echo number_format(round($ownsp));
      echo "</td></tr>";
    }                            
  }
?>
            </tbody>
          </table>  
        </div>
      </div>
    </div>  
  </body>
<script>                    
$(function(){     
  // Enables popover with html content
  $("[data-toggle=popover]").popover({
    html:true
  });
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
  xhttp.open("GET", "<?echo $ajaxfile;?>?SteemitUser=" + username, true);
  xhttp.send();
}
function goToBtn() {
	goToPage = document.getElementById("goToPage").value;
	window.location.href = 'ranking.php?mode=<?echo $mode;?>&page='+goToPage;
}
</script>
</html>
