<?php


if (!isset($_GET['test_no']))
{
  include 'navigation.php';
  include 'select_test_relevence.php';
}
else
{
 

  function query_the_database($sql_query, $con)
  {
      $re = mysql_query($sql_query);

      if (!$re) {
        echo 'Could not run query: ' . mysql_error();
        exit;
      }

      return $re;
  }
  function print_divs($table_name, $colour)
  {
    echo '
     <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""
    "http://www.w3.org/TR/html4/loose.dtd">

    <html>
      <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title>Print_divs</title>
          
        <link rel="stylesheet" href="colourcss.css" type="text/css" media="screen" charset="utf-8">
        
      </head>
      <body>';
    

      $test_1_record = array();

      $con = mysql_connect("localhost","dandysea","fake password");
      /*$con = mysql_connect("localhost","root","");*/
      if (!$con)
      {
      die('Could not connect: ' . mysql_error());
      }

      mysql_select_db('dandysea_evaluation_results', $con);
      /*mysql_select_db('evaluation_results', $con);*/

      if (!$con) 
      {
      die ('Can\'t use evaluation_results : ' . mysql_error());
      }


      
      /**
       * fill test 1 arrays
       **/

      $sql_query = "SELECT * FROM $table_name";
      $re = query_the_database($sql_query, $con);

      for ($i = 0; $i < 50; $i++) 
      {
        $result[] = mysql_fetch_row($re);
        /*$test_1_precison[] = $result[$i][2];*/
        /*$test_1_average_precison[] = $result[$i][3];*/
        /*$test_1_precison_at_n[] = $result[$i][4];*/
      }

      /*print_r ($result);*/


      for ($i = 0; $i < 50; $i++) 
      {
        echo ('<div class = "one_query"> ');
        for ($j = 0; $j < 150; $j++) 
        {
          if($result[$i][$j] == 0)
          {
            echo ('<div class="zero"> </div>');
          }

          if($result[$i][$j] == 1)
          {
            if($colour === 1)
            {
              echo ('<div class="one_red"> </div>');
            }
            if($colour === 2)
            {
              echo ('<div class="one_blue"> </div>');
            }
       
            /*echo ('hello');*/
          }

          if($result[$i][$j] == -1)
          {
            echo ('<div class="minus_one"> </div>');
          }
        }
        echo  ('</div>');

      }

    echo '
      </body>
      </html>';
  $result = array();
  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='1')
  {
    include 'navigation.php';
    Print_divs('test_1_relevance_record_location', 1);

    echo '1: Non Aggregated with no Stemming and no Stopword Removal';

    echo '<br>  </br>';
    echo '<br>  </br>';
    echo '<a href="print_divs.php?test_no=3">Click to view 3:  Aggregated with no Stemming and no Stopword Removal</a>';

  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='2')
  {
    include 'navigation.php';
    Print_divs('test_2_relevance_record_location', 2);

    echo '2: Non Aggregated with Stemming and Stopword Removal turned on';

    echo '<br>  </br>';
    echo '<br>  </br>';
    echo '<a href="print_divs.php?test_no=4">Click to view 4:  Aggregated with Stemming and Stopword Removal turned on</a>';

  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='3')
  {
    include 'navigation.php';
    Print_divs('test_3_relevance_record_location', 1);

    echo '3: Aggregated with no Stemming and no Stopword Removal';

    echo '<br>  </br>';
    echo '<br>  </br>';
    echo '<a href="print_divs.php?test_no=1">Click to view 1:  Non Aggregated with no Stemming and no Stopword Removal</a>';

  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='4')
  {
    include 'navigation.php';
    Print_divs('test_4_relevance_record_location', 2);

    echo '4: Aggregated with Stemming and Stopword Removal turned on';

    echo '<br>  </br>';
    echo '<br>  </br>';

    echo '<a href="print_divs.php?test_no=2">Click to view 2:  Non Aggregated with Stemming and Stopword Removal turned on</a>';
  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='5')
  {
    include 'navigation.php';
    Print_divs('test_1_relevance_record_location', 1);
    echo '1: Non Aggregated with no Stemming and no Stopword Removal';
    echo '<br>  </br>';

    Print_divs('test_3_relevance_record_location', 1);
    echo '3: Aggregated with no Stemming and no Stopword Removal';

  }

  if (isset($_GET['test_no']) && $_GET['test_no'] ==='6')
  {
    include 'navigation.php';

    Print_divs('test_2_relevance_record_location', 2);
    echo '2: Non Aggregated with Stemming and Stopword Removal turned on';
    echo '<br>  </br>';
    Print_divs('test_4_relevance_record_location', 2);
    echo '4: Aggregated with Stemming and Stopword Removal turned on';

  }

}

?>


