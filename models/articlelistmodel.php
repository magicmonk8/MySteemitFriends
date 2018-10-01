<?php

class articlelistmodel {
	
	protected $db;
	
	public function __construct(PDO $db) {
		
		$this->db = $db;
	}
	
	public function gethistory($date,$todate,$voter) {
		
		$sql = "SELECT timestamp,weight,author,permlink
				FROM TxVotes 
				WHERE (voter=:voter) 
					AND (timestamp>=Convert(datetime, :date)) 
					AND (timestamp<=Convert(datetime,:todate)) 
				ORDER BY timestamp DESC";
		
		$result = $this->db->prepare($sql);
		
		$result -> bindValue(':date', $date, PDO::PARAM_STR);
		$result -> bindValue(':todate', $todate, PDO::PARAM_STR);
		$result -> bindValue(':voter', $voter, PDO::PARAM_STR);
		
		$result->execute();
		
		return $result;
	}
	
	public function getwritten($date,$todate,$author) {
		$sql = "SELECT author,permlink,created AS timestamp
				FROM Comments 
				WHERE (author =:author) 
				AND (created>=Convert(datetime, :date)) 
				AND (created<=Convert(datetime,:todate))
				ORDER BY created DESC";
		$result = $this->db->prepare($sql);	
		$result -> bindValue(':date', $date, PDO::PARAM_STR);
		$result -> bindValue(':todate', $todate, PDO::PARAM_STR);
		$result -> bindValue(':author', $author, PDO::PARAM_STR);
		$result->execute();
		return $result;
	}
	
	public function close_connection() {
        if(isset($this->db))
        {
            $this->db->close();
            unset($this->db);
        }
    }
	
}