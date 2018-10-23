<!-- Navigation bar for all pages on Steemfriends -->
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
    		<a class="dropdown-item" href="ranking.php?mode=ownSP">Own SP</a>
    		<a class="dropdown-item" href="ranking.php?mode=sbd">SBD</a>	
    		<a class="dropdown-item" href="accountvalue.php">Estimated Account Value</a>     
       	<a class="dropdown-item" href="pending_payout.php">Pending Payout</a>
       	<a class="dropdown-item" href="past_payout.php">Past Payout</a>  
       	<a class="dropdown-item" href="powerdown.php">Power Down</a> 
       	<a class="dropdown-item" href="witnessvoting.php">Witness Voting Power: All Users</a>          
       	<a class="dropdown-item" href="witnessproxies.php">Witness Voting Power: Proxies</a>
        <a class="dropdown-item" href="ranking.php?mode=accountCreation">Account Creation</a>   
      </div>
    </div><!-- /btn-group -->
    <a class="btn btn-lg btn-danger navbutton nounderline"  href="upvotelist.php">$ Calculator</a>
    <a class="btn btn-lg btn-secondary navbutton nounderline"  href="articlelist.php">User History</a>
  </div> 
</nav>    
