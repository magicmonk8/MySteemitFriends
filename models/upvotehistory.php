<?php

class upvotehistory {
	
	protected $db;
	
	public function __construct(PDO $db) {
		
		$this->db = $db;
	}
	
	public function gethistory($date,$todate,$voter) {
		
		$sql = "SELECT voter,permlink,timestamp,weight,author FROM TxVotes WHERE (voter=:voter) AND (timestamp>=Convert(datetime, :date)) AND (timestamp<=Convert(datetime,:todate)) ORDER BY timestamp DESC";
		
		$sth = $this->db->prepare($sql);
		
		$sth -> bindValue(':date', $date, PDO::PARAM_STR);
		$sth -> bindValue(':todate', $todate, PDO::PARAM_STR);
		$sth -> bindValue(':voter', $voter, PDO::PARAM_STR);
		
		$sth->execute();
		
		return $sth;
	}
	
	public function close_connection() {
        if(isset($this->db))
        {
            $this->db->close();
            unset($this->db);
        }
    }
	
}