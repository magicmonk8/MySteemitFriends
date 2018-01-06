<html>

  <head>

    <title>My Steemit Friends</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

    <script src="jquery/jquery-3.2.1.min.js"></script>

    <script src="extensions/popper.min.js"></script>

    <script src="bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="style.css">

    <style>



    a.page-link{

 

    color:blue;

    }

    a.page-link:visited {

      color:blue;

    }

    ul {

    margin:0.5rem;

    }

    li {     

    

   

    }

    

    a.btn-info, a.btn-info:visited, a.btn-primary, a.btn-primary:visited {

    color:white;

    } 

    a.btn-light {

      color:blue;

    }

    a.btn-light:visited {

      color:blue;

    }

    

    </style>

  </head>

  <body>   

    <div class="container-fluid bg-1 text-center" style="max-width:1000px;">

    <div class="row">

     <div class="col">

     

<h1>Steemit Followers Ranking</h1>       

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

$servername = "sql.steemsql.com:1433";

$username = "steemit";

$password = "steemit";

$numberofpages=7;

$numberofrows=10;

$pagesize=50;

if ($_GET["page"]) {

$page = $_GET["page"];

} else {$page=1;}





if ($_GET["highlight"]) {

$highlight = $_GET["highlight"];

}









// where to start the results

$offset = ($page - 1) * $pagesize;





echo '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';



if ($page>=4) {

for ($x=$page-3;$x<=$page+3;$x++) {      

      if ($x==$page) {

        echo '<li class="page-item active"><a class="page-link" href="followers.php?page='.$x.'">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="followers.php?page='.$x.'">'.$x.'</a></li>';

      }  

  }

}  else {

  for ($x=1;$x<=$numberofpages;$x++) {      

      if ($x==$page) {

        echo '<li class="page-item active"><a class="page-link" href="followers.php?page='.$x.'">'.$x.'</a></li>';

      } else {

        echo '<li class="page-item"><a class="page-link" href="followers.php?page='.$x.'">'.$x.'</a></li>';

      }  

  }    

} 

echo '</ul></nav><br>';

if ($page>1) {

echo '<a href="followers.php?page='.($page-1).'" class="btn btn-light" role="button">Previous Page</a> ';

}

echo '<a href="followers.php?page='.($page+1).'" class="btn btn-light" role="button">Next Page</a><br><br>'; 



echo '<form action="followers.php" method="get">Go To Page Number <input type="text" name="page" size="5"> <input type="submit" value="Go"></form><br></div>';

// Use try catch exception handling. Details: https://www.w3schools.com/PhP/php_exception.asp

try {

    // connect to SteemSQL via PDO. Make sure pdo and pdo_dblib extensions are enabled.

    $conn = new PDO("dblib:host=$servername;dbname=DBSteem", $username, $password);

    

    // set the PDO error mode to exception

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    

    // print connection successful message if connection successful.

/*    if ($conn) {

        echo "Connection to database established.<br>";

    }

    */

    // test query. Select the name column from the Accounts table where the Id is 29666. Result should be magicmonk.

    $sql = "select count(*), Following

from Followers

group by following

Order by count(*) DESC

OFFSET ".$offset." ROWS

FETCH NEXT ".$pagesize." ROWS ONLY;";

    

    // execute the query. Store the results in sth variable.

    $sth = $conn->prepare($sql);

    $sth->execute();

    echo '</div><div class="col">';

echo '<table id="bigtable" class="table table-sm" style="background-color:#0f4880;border:5px solid white">';

    

echo '<thead class="thead-default mobile"><tr><th style="text-align: center;">Ranking</th><th>User Name</th><th>Followers</th></tr></thead>';

    // print the results. If successful, magicmonk will be printed on page.

    $rank=$pagesize*($page-1)+1;

    while ($row = $sth->fetch(PDO::FETCH_NUM)) { 

      echo '<tr><td style="text-align: center;">';

      echo $rank;

      $rank++;

      echo "</td><td>";

      if ($row[1]==$highlight) {

      echo '<span style="background-color:red">';

      } 

      echo '<a tabindex="0" data-trigger="click" data-toggle="popover" data-content="<p><a class=\'nounderline btn btn-primary\' href=\'http://steemit.com/@'.$row[1].'/\'>Steemit Profile</p><a class=\'nounderline btn btn-info\' href=\'index.php?User='.$row[1].'\'>MSF Profile</a>">'.$row[1].'</a>';

      if ($row[1]==$highlight) {

      echo '</span>';

      } 

          

          echo "</td><td>";

          echo $row[0];

          echo "</td></tr>";

          

        }

        echo "</table>";

}

// if cannot connect to database, print error message    

catch(PDOException $e)  {

    echo "Connection failed: " . $e->getMessage();

}

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

  xhttp.open("GET", "get_follower_rank.php?SteemitUser=" + username, true);

  xhttp.send();

}

</script>







</html>
