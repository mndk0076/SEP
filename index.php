<?php

  /*
    Note: After using the Google Custom Search API for a while, I found that it does not produce results that are as 
    consistent as those found on google.ca. I was unable to locate a reliable API service, so I changed my strategy 
    to data scraping. Although the automatic scraping now in place operates flawlessly, there could be a problem down 
    the road: if the application is used abusively, Google might block the user's IP address. Currently, this problem 
    is mitigated by changing the user_agent with each request. To guarantee that Google cannot prevent data requests 
    coming from the application in the future, a solution is required by using an service than can bypass Google bot
    recognition and captchas. Last, there is no validation in the input fields.

    Live site works now since I added a proxy to it. but keep in mind the rankng is dependable in location of the proxy used.
  */

  require 'vendor/autoload.php';

  $result_msg = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // list of user_agents to ramdomized so google can't block the parser request easily
    $user_agent_list = [
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Safari/605.1.15",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0"
    ];
    $keyword = $_POST['keyword'];
    $siteURL = $_POST['url'];

    $random_user_agent = $user_agent_list[array_rand($user_agent_list)]; // randomized the user_agent

    // Scraper
    $web = new \Spekulatius\PHPScraper\PHPScraper;
    $web->go("https://www.google.ca/search?q=" . urlencode($keyword) . "&gl=ca&hl=en&num=100"); // use google.ca and returns 100 results
    $web->setConfig([
      'agent' => $random_user_agent 
    ]);

    $keyword_ranking = 0;
    $counter = 1;

    // Loop through all the links, Google had made it hard to use the xPath filter to interpret the data
    // As a result, I looked into other strategies and finally discovered an algorithm that worked well using the links. 
    foreach ($web->links as $link) {
      if (strpos($link, 'https://www.google.ca/url?q=') !== false) {
        if (strpos($link, 'https://maps.google.ca/') === false && strpos($link, 'https://www.google.ca/url?q=/search') === false) {
          if (strpos($link, $siteURL) !== false) {
            $keyword_ranking = ($counter === 1) ? 1 : ($keyword_ranking > 0 ? min($keyword_ranking, $counter) : $counter ); // set the keyword ranking 
          }
          $counter++;
        }
      }
    }

    // Result message
    if($keyword_ranking === 0){
      $result_msg =  "<span>$siteURL</span> for the keyword <span>'$keyword'</span> is not in top 100";
    }else{
      $result_msg =  "The ranking of <span>$siteURL</span> for the keyword <span>'$keyword'</span> is: <span>#$keyword_ranking</span>";
    }

  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <title>SEP</title>
  </head>
  <body>
    <main>
      <div class="container">
        <form action="" method="post">
          <input type="text" name="keyword" id="keyword" placeholder="Your keyword" required />
          <input type="text" name="url" id="url" placeholder="example.com" required/>

          <input type="submit" value="Research" class="submit" />
        </form>
      </div>
      <div class="result_msg">
        <?php echo $result_msg; ?>  
      </div>
    </main>
  </body>
</html>