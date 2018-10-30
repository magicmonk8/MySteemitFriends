<?php
class articlelistmodel {
  protected $db;
  public function __construct(PDO $db) {
    $this->db = $db;
  }
  public function gethistory($date,$todate,$voter,$articlesonly,$tag,$title) {
    // retrieve list of articles voted on by user
    $sql = "SELECT timestamp,weight,TxVotes.author,TxVotes.permlink
            FROM TxVotes LEFT OUTER JOIN Comments
            ON (TxVotes.author=Comments.author AND TxVotes.permlink=Comments.permlink)
            WHERE (voter=:voter)
            AND (timestamp>=Convert(datetime, :date))
            AND (timestamp<=Convert(datetime,:todate))";
      // if comments are excluded, add this to SQL
    if ($articlesonly==2) {
      $sql.= "AND (Comments.depth=0)";
    }
      // if there is a tag specified, add this to SQL
    if ($tag!=NULL) {
      $sql.="AND (CONTAINS(Comments.json_metadata, '".$tag."'))";
    }
    if ($title!=NULL) {
      $sql.="AND (CONTAINS(Comments.title, '".$title."'))";
    }
    $sql.="ORDER BY timestamp DESC";
    $result = $this->db->prepare($sql);
    $result -> bindValue(':date', $date, PDO::PARAM_STR);
    $result -> bindValue(':todate', $todate, PDO::PARAM_STR);
    $result -> bindValue(':voter', $voter, PDO::PARAM_STR);
    $result->execute();
    return $result;
  }
  public function getwritten($date,$todate,$author,$articlesonly,$tag,$title) {
    // retrieve list of articles written by user
    $sql = "SELECT author,permlink,created AS timestamp
            FROM Comments
            WHERE (author =:author)
            AND (created>=Convert(datetime,:date))
            AND (created<=Convert(datetime,:todate))
            ";
    // if comments are excluded, add this to SQL
    if ($articlesonly==2) {
      $sql.= "AND (depth=0)";
    }
    // if there is a tag specified, add this to SQL
    if ($tag!=NULL) {
      $sql.="AND (CONTAINS(json_metadata, '".$tag."'))";
    }
    // if there is a title specified, add this to SQL
    if ($title!=NULL) {
      $sql.="AND (CONTAINS(title, '".$title."'))";
    }
    $sql.="ORDER BY created DESC";
    $result = $this->db->prepare($sql); 
    $result -> bindValue(':date', $date, PDO::PARAM_STR);
    $result -> bindValue(':todate', $todate, PDO::PARAM_STR);
    $result -> bindValue(':author', $author, PDO::PARAM_STR);
    $result->execute();
    return $result;
  }
  public function close_connection() {
    if(isset($this->db)) {
      $this->db->close();
      unset($this->db);
    }
  }
}
