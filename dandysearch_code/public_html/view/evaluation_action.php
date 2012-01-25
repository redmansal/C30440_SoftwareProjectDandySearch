<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""
"http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
  
    <link rel="stylesheet" href="view/css/master.css" type="text/css" media="screen" charset="utf-8">

    <link rel="shortcut icon" href="view/images/icon.ico" />

    <title>Index</title>
        
  </head>
  <body>

    
    <div id="container_main">
      <?php
      include 'view/header.php';
      ?>
      <div class="top_of_screen">
        
      <div class="white_left_margin">
      </div>

      <div class="danty_search_text">
        
      </div>

      <div class="white_left_margin">
      </div>
      </div>

      <div style="clear:both;"></div>

    </div>

   <div id="container_main">

      <div class="main_search">
        <div class="search_white_left_margin">
        </div>

        <div class="surround_form">
          <form name="input" action="index_evaluation.php" method="get">
            <input type="hidden" id="admin" name="source_page" value="perform_evaluation" />

            <input type="radio" name="test_no" value="1" /> Test 1 
            <input type="radio" name="test_no" value="2" /> Test 2
            <input type="radio" name="test_no" value="3" /> Test 3
            <input type="radio" name="test_no" value="4" /> Test 4 

            <input type="submit" value="Perform Evalution of Meta Search Engine"/>
          </form>

        </div>   

        <div class="search_white_right_margin">
        </div>


        </div>

        <div class="below_search">
          
        </div>
        <div style="clear:both;"></div>
      </div>
     
      <div id="container_main">
      <?php
      include 'view/footer.php';
      ?>

        


      <div style="clear:both;"></div>
      </div>

    <!--<h1 id="heading">NB NB do a completedesign of the site allowing for all the deliverables in the brief using interfaces 
                      make a log of events that need to be recorded
                  Also data scrape Ask.com as the third search engine</h1>
-->

  </body>

</html>

