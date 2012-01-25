<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""
"http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
  
    <link rel="stylesheet" href="view/css/master.css" type="text/css" media="screen" charset="utf-8">

    <title>Index</title>
        
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


       
      <form name="input" action="index.php" method="get">
        <input size = 120 type="text" name="user_query"/>
        <input type="hidden" id="admin" name="source_page" value="aggregated_options" />
        <input type="checkbox" name="stopword_on" value="yes" /> Turn on Stopword Removal on the query<br />
        <input type="checkbox" name="stemming_on" value="yes" /> Turn on Stemming on the query<br /> 
        <input type="checkbox" name="remove_puncuation_on" value="yes" /> Turn on Punctuation removal on the query<br /> 
        
        <input type="submit" value="Search"/>
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

