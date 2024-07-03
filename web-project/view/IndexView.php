<?php
require_once 'utils/TextProcessingService.php'; 


class IndexView {
    
    private $textProcessingService; 
    
    public function __construct() {
        $this->textProcessingService = new TextProcessingService(); 
    }
    
    public function render($showUserIcon) {
?>
<!DOCTYPE html>
<html class="mainpage" lang="en">
<head>
  <meta charset="utf8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resource finder</title>
  <link href="css/styleHomepage.css" rel="stylesheet" type="text/css"> 
  <script async src="https://cse.google.com/cse.js?cx=63412cb3119184f92"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header>
  <nav class="navbar">
    <a href="index.php">
      RESOURCE FINDER
    </a>
    <a href="view/support.php">What we do?</a> 
  </nav>

  <?php
    if($showUserIcon) {
      echo "<div class=\"icons\">";
      echo "<a href=\"view/accountdetails.php\" class=\"fas fa-user\"></a>";
      echo "</div>";
    } else {
      echo "<div class=\"login-signin\">";  
      echo "<div class=\"login\">";
      echo "<a href=\"view/login.php\">Log in</a>";
      echo "</div>";
      echo "<div class=\"signin\">";
      echo "<a href=\"view/signin.php\">Sign up</a>";
      echo "</div>";
      echo "</div>";
    }
  ?>

</header>

<br><br><br><br>
  <main>
    <div class="intro">
      <h2>resources and answers you can trust. </h2>
      <form class="search-bar" action="view/postlist.php" method="get">
        <input type="text" name="subject" placeholder="tell us something you are curious about:" required>
        <button type="submit">Search</button>
      </form>
    </div>
    
    <div class="categories">
      <h2> articles that might interest you</h2>
      <form class="input-grid" action="view/postlist.php" method="get">
        <input type="submit" name="subject" value="Object oriented programming c++">
        <input type="submit" name="subject" value="Tips for focusing while programming">
        <input type="submit" name="subject" value="I want to create games, what's next?">
        <input type="submit" name="subject" value="Books to boost your understanding and logic">
        <input type="submit" name="subject" value="Popular services in 2024">
        <input type="submit" name="subject" value="Preferred programming languages">
      </form>
    </div>

    <div class="search-results">
      <?php
      if(isset($_GET['subject'])) {
          $inputText = $_GET['subject'];
          $results = $this->textProcessingService->processText($inputText);
          
          if(!empty($results)) {
              echo "<h3>Search results for: $inputText</h3>";
              echo "<ul>";
              foreach($results as $result) {
                  echo "<li><a href=\"" . $result['url'] . "\">" . $result['title'] . "</a></li>";
              }
              echo "</ul>";
          } else {
              echo "<p>No results found for: $inputText</p>";
          }
      }
      ?>
    </div>

  </main>
</body>
</html>
<?php
  }
}
?>
