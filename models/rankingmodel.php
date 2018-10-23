<?php                    
class rankingmodel {  
	protected $db;  
	public function __construct(PDO $db) {		
		$this->db = $db;
	}
  // method for obtaining ranking for sbd or own SP                                                   
	public function getValueRank($mode,$offset,$pagesize) {
    $sql = "
      SELECT convert(float, a.vesting_shares)-convert(float,a.delegated_vesting_shares)+convert(float,a.received_vesting_shares) AS effective_vests, a.name, convert(float, a.vesting_shares) AS vests, convert(float,sbd_balance) + convert(float,savings_sbd_balance) AS sbd
      FROM
      (SELECT name, Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares)) AS vesting_shares, Substring(delegated_vesting_shares,0,PATINDEX('%VESTS%',delegated_vesting_shares)) AS delegated_vesting_shares, Substring(received_vesting_shares,0,PATINDEX('%VESTS%',received_vesting_shares)) AS received_vesting_shares, Substring(sbd_balance,0,PATINDEX('%SBD%',sbd_balance)) AS sbd_balance, Substring(savings_sbd_balance,0,PATINDEX('%SBD%',savings_sbd_balance)) AS savings_sbd_balance
      FROM Accounts (NOLOCK)) a
    ";
    if ($mode=="sbd") {
      $sql.="
      Order by sbd DESC
      ";
    } elseif ($mode=="ownSP") {
      $sql.="
      Order by vests DESC
      ";    
    }    
    $sql.="      
      OFFSET :offset ROWS
      FETCH NEXT :pagesize ROWS ONLY;
    ";
    $result = $this->db->prepare($sql);        
    $result -> bindValue(':offset', $offset, PDO::PARAM_INT);
   	$result -> bindValue(':pagesize', $pagesize, PDO::PARAM_INT);		
		$result -> execute();                                          		
		return $result;                                                
  }
  // method for obtaining ranking for account creation
  	public function getAccountCreation($offset,$pagesize) {
    $sql = "
      SELECT name, convert(float,Substring(vesting_shares,0,PATINDEX('%VESTS%',vesting_shares))) AS vests, created, mined
      FROM Accounts (NOLOCK)
      ORDER BY created ASC, name ASC
      OFFSET :offset ROWS
      FETCH NEXT :pagesize ROWS ONLY;
    ";
    $result = $this->db->prepare($sql);        
    $result -> bindValue(':offset', $offset, PDO::PARAM_INT);
   	$result -> bindValue(':pagesize', $pagesize, PDO::PARAM_INT);		
		$result -> execute();                                          		
		return $result;                                                
  }                         
  public function close_connection() {
    if(isset($this->db)) {
      $this->db->close();
      unset($this->db);
    }
  }  
}
