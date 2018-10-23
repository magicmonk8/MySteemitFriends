<?php
// connect to SteemSQL database
include 'steemSQLconnect2.php';	
// make your model available
include 'models/rankingmodel.php';
// retrieve global values for calculating Steem Power	
$my_file = fopen("global.txt",'r');
$total_vesting_fund_steem=fgets($my_file);
$total_vesting_fund_steem = preg_replace('/[^0-9.]+/', '', $total_vesting_fund_steem);
$total_vesting_shares=fgets($my_file);
$total_vesting_shares = preg_replace('/[^0-9.]+/', '', $total_vesting_shares);
fclose($my_file);
// what is the current page? If page number not set, equal 1  
if (isset($_GET["page"])) {  
  // sanitize input with filter_var, make sure the input is an integer.
  $page = filter_var($_GET["page"],FILTER_VALIDATE_INT);          
} else {
  $page=1;
}
// which type of ranking is required?
if (isset($_GET["mode"])) {
  $mode =  filter_var($_GET["mode"], FILTER_SANITIZE_STRING);
} else {
  $mode = "none";
}                
// is there a user highlighted?
if (isset($_GET["highlight"])) {  
  $highlight = strtolower($_GET["highlight"]);  
} 
// number of users listed on each page  
$pagesize=50;  
// where to start the results  
$offset = ($page - 1) * $pagesize;
// create an instance
$rankingmodel = new rankingmodel($conn);
// get list of results
if ($mode=="sbd" or $mode=="ownSP") {
  $results = $rankingmodel -> getValueRank($mode,$offset,$pagesize);
} elseif ($mode=="accountCreation") {
  $results = $rankingmodel -> getAccountCreation($offset,$pagesize);
} 
// show the view
include 'views/rankingview.php';                          
$rankingmodel -> close_connection();                                
?>
