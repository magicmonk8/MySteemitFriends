<html>
  <head>
  <title>Steem Friends</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <script src="jquery/jquery-3.2.1.min.js"></script>
  <script src="popper.min.js"></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="style.css?3">
  <style>
    .navbutton {
      width: 10rem;
      margin: 0.5rem;
    }
  </style>
  </head>
  <body>
<!-- navbar -->
<? include 'views/navbar.php'; ?> 
    <!-- main page container -->
    <div class="container-fluid bg-1 text-center">  
<? include 'views/notifications.php'; ?>
      <!-- form for obtaining user input -->
      <form class="form-inline justify-content-center" onSubmit="return false">
        <div class="form-group" style="margin-top:10px;">
          <label for="voter">User:&nbsp;</label>
          <input class="form-control" placeholder="<?
  if ($mode=="upvote") {
    echo "Voter";
  } else {
    echo "Author";
  }
          ?> Username" id="voter" type="text" size="15" name="voter" value="<? if (isset($voter)) {echo $voter;} ?>" autofocus>&nbsp;&nbsp;
        </div>
        <div class="form-group" style="margin-top:10px;">
          <label for="fromDate">From Date:&nbsp;</label>
          <input class="form-control" name="date" id="date" type="date" value="<? if (isset($newdate)) {echo $newdate;} else {echo $date;} ?>" id="date" min="2016-03-30" max="<?echo date(" Y-m-d "); ?>">&nbsp;&nbsp;
        </div>
        <div class="form-group" style="margin-top:10px;">
          <label for="toDate">To Date:&nbsp;</label>
          <input class="form-control" name="toDate" id="toDate" type="date" value="<? if (isset($todate)) {echo $todate;} else {echo date(" Y-m-d ",strtotime("+1 day "));} ?>" id="toDate" min="2016-03-30" max="<?echo date(" Y-m-d ",strtotime("+1 day ")); ?>">&nbsp;&nbsp;
        </div>
        <div class="form-group" style="margin-top:10px;">
          <label for="tag">Must Contain Tag(s) Separated By Space:&nbsp;&nbsp;</label>		  			
          <input class="form-control" placeholder="e.g. utopian-io development" name="tag" id="tag" type="text" size="25" value="<? if (isset($tag)) {$arrlength = count($tag);for($x = 0; $x < $arrlength; $x++) {echo $tag[$x];echo " ";}}?>" aria-describedby="tagHelp"">&nbsp;&nbsp;<br>		 
        </div>       
        <div class="form-group" style="margin-top:10px;">
          <label for="title">Title Must Contain Word(s):&nbsp;&nbsp;</label>
          <input class="form-control" name="title" id="title" type="text" size="20" value="<?if (isset($title)) {echo $title;}?>">&nbsp;&nbsp;
        </div>
        <div class="form-group" style="margin-top:10px;">
          <input class="form-control" name="Articlesonly" type="checkbox" value="2" id="Articlesonly"<? if ($articlesonly==2) {echo " checked";} ?>>&nbsp;
          <label for="Articlesonly">Exclude Comments&nbsp;&nbsp;</label>  
        </div>
      </form>
       <?
      if ($mode=="upvote") {
        echo '<button id="upvotebtn" class="btn btn-lg btn-primary" style="margin-top:10px;">List Articles Voted</button>';        
      } else {
        echo '<button id="writtenbtn" class="btn btn-lg btn-primary" style="margin-top:10px;">List Articles Written</button>';
      }
      ?>
      <br><br>
<?
// title at the top of page to state the voter and who is the author  
if (isset($results)) {
  // depending on whether the user clicked Voted or Written, display different table headings.
  if ($mode=='upvote') {
    echo '<p><a href="http://steemit.com/@'.$voter.'"><b>@'.$voter.'</b></a> upvoted the following:</p>';
    $headings=array("Timestamp", "%", "Author", "Link");
  } 
  if ($mode=='written') {
    if ($voter!=NULL) {
      echo '<p><a href="http://steemit.com/@'.$voter.'"><b>@'.$voter.'</b></a> wrote the following:</p>';
    }
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
  echo '<a href="https://steemit.com/@'.$row['author'].'" target="_top">'.$row['author'].'</a>';
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
    // Retrieve input to be submitted via Javascript
    function retrieveInput() {
      title = document.getElementById("title").value;    
      tag = document.getElementById("tag").value;
      goToUser = document.getElementById("voter").value;
      date = document.getElementById("date").value;
      toDate = document.getElementById("toDate").value;
      // find out whether comments are included in article list
      mycheck = document.getElementById("Articlesonly").checked;
      if (mycheck==true) {
        mycheck=2;
      } else 
      {
        mycheck=1;
      }
    }
    
    // Submit input from user to articlelist controller
    $(function(){
      $("#upvotebtn").click(function() {
        retrieveInput();
      window.location.href = 'articlelist.php?mode=upvote&Articlesonly='+mycheck+'&voter='+goToUser+'&date='+date+'&toDate='+toDate+'&tag='+tag+'&title='+title;
      });
      
      $("#writtenbtn").click(function() { 
      retrieveInput();
      window.location.href = 'articlelist.php?mode=written&Articlesonly='+mycheck+'&voter='+goToUser+'&date='+date+'&toDate='+toDate+'&tag='+tag+'&title='+title;       
      });         
    });           
  </script> 
</html>
