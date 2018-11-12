<?php
class articlelistmodel {
  protected $db;
  public function __construct(PDO $db) {
    $this->db = $db;
  }
  public function gethistory($date,$todate,$voter,$articlesonly,$tag,$tag2,$title) {
    // retrieve list of articles voted on by user
    $sql = "SELECT top 500 timestamp,weight,TxVotes.author,TxVotes.permlink
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
    if ($tag2!=NULL) {
      $sql.="AND (CONTAINS(Comments.json_metadata, '".$tag2."'))";
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
  public function getwritten($date,$todate,$author,$articlesonly,$tag,$tag2,$title) {
    // retrieve list of articles written by user
    $sql = "SELECT top 500 author,permlink,created AS timestamp
            FROM Comments
            WHERE (created>=Convert(datetime,:date))
            AND (created<=Convert(datetime,:todate))
            ";
    // if author is defined, add this to SQL
    if ($author!=NULL) {
      $sql.="AND (author =:author)";
    }                                           
    // if comments are excluded, add this to SQL
    if ($articlesonly==2) {
      $sql.= "AND (depth=0)";
    }
    // if there is a tag specified, add this to SQL
    if ($tag!=NULL) {
      $sql.="AND (CONTAINS(json_metadata, '".$tag."'))";
    }
    if ($tag2!=NULL) {
      $sql.="AND (CONTAINS(json_metadata, '".$tag2."'))";
    }
    // if there is a title specified, add this to SQL
    if ($title!=NULL) {
      $sql.="AND (CONTAINS(title, '".$title."'))";
    }
    $sql.="ORDER BY created DESC";
    $result = $this->db->prepare($sql); 
    $result -> bindValue(':date', $date, PDO::PARAM_STR);
    $result -> bindValue(':todate', $todate, PDO::PARAM_STR);
    if ($author!=NULL) {
      $result -> bindValue(':author', $author, PDO::PARAM_STR);
    }
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
