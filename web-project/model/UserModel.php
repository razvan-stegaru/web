<?php
class UserModel {
  private $conn; 


  public function __construct($dbConn) {
    $this->conn = $dbConn;
  }


  public function getUserStatus() {
    if(isset($_SESSION["userID"])) {
        return true;
    } else {
        return false;
    }
  }


  public function getUserById($id) {
    $sql = "SELECT * FROM users WHERE usersID = :id";
    $stmt = oci_parse($this->conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);

    oci_execute($stmt);
    $result = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
    return $result;
  }
}
?>
