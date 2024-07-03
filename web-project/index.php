<?php
session_start();
require_once('model/UserModel.php');
require_once('view/IndexView.php');
require_once('model/dbh-inc.php');

class IndexController {
  private $userModel; 

 
  public function __construct() {
    global $conn;
    $this->userModel = new UserModel($conn);
  }

  public function index() {
    $showUserIcon = $this->userModel->getUserStatus(); 
    $indexView = new IndexView(); 
    $indexView->render($showUserIcon); 
  }
}


$controller = new IndexController();
$controller->index();

