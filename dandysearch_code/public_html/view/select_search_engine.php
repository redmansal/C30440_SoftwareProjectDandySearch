<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""
"http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">

    <link rel="stylesheet" href="view/css/master.css" type="text/css" media="screen" charset="utf-8">
    <title>Select_search_engine</title>
    
  </head>
  <body>
    <div id="container_main">
    <?php
    include 'view/header.php';
    ?>
    <div class = "wide_band">
    </div>

    <div class="stretch_white">
      
        <div class="search_white_right_margin">
        </div>
        <div class="surround_select_search_type">
          
          <form action="index.php" method="get" accept-charset="utf-8">
            <input type="hidden" id="admin" name="source_page" value="select_search_type" />
            <input type="radio" name="select_option" value="bing_yahoo_blekko_google"> Show Search Results for Bing, Yahoo, Blekko and Google in separate lists<br/>
            <input type="radio" name="select_option" value="bing_yahoo_blekko_merged">  Show a non aggregated list with Bing, Yahoo, and Blekko Search Results merged <br/>
            <input type="radio" name="select_option" value="aggregated"> Show an aggregated list with Bing, Yahoo, and Blekko Search Results merged  <br/>
          
            <p><input type="submit" value="Continue &rarr;"></p>


          </form>
      
        </div>


        <div class="search_white_right_margin">
        </div>


    </div>

        <div style="clear:both;"></div>
    </div>

    <div id="container_main">

    <div class = "wide_band">
    </div>
    <?php
    include 'view/footer.php';
    ?>

    <div style="clear:both;"></div>
    </div>
  </body>
</html>

