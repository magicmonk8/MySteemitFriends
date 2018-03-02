<?php

// connect to SteemSQL database
include 'steemSQLconnect2.php';	

// Make your model available
include 'models/upvotehistory.php';


// Get values from URL	
// voter details stripped from URL
if ($_GET["voter"]) {
	$voter = rtrim($_GET["voter"]);
}
	
// check if date has been entered (submitted via form)	
if ($_GET["date"]) {
	$date = $_GET["date"];
} else {
	// set date variable for SQL query.
	$date = date("Y-m-d", strtotime("-1 months"));
}

// check if to date has been entered (submitted via form)
if ($_GET["toDate"]) {
	$todate = $_GET["toDate"];
} else { 
	$todate = date("Y-m-d");	
}
	
// retrieve choice for whether to include articles only or to include comments as well
if ($_GET["Articlesonly"]) {
	$articlesonly = $_GET["Articlesonly"];
} else {
	$articlesonly = 1;
}

// create an instance
$upvotehistory = new upvotehistory($conn);

if ($voter) {
// get list of results
$results = $upvotehistory -> gethistory($date,$todate,$voter);
}

// Show the view
include 'views/articlelist.php';

$upvotehistory -> close_connection();

?>