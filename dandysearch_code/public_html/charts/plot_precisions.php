<?php
if (!isset($_GET['chart_letter']))
{
  include "navigation.php";
  include "select_chart.php";
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

  $test_1_precison = array();
  $test_1_average_precison = array();
  $test_1_precison_at_n = array();

  $test_2_precison = array();
  $test_2_average_precison = array();
  $test_2_precison_at_n = array();

  $test_3_precison = array();
  $test_3_average_precison = array();
  $test_3_precison_at_n = array();

  $test_4_precison = array();
  $test_4_average_precison = array();
  $test_4_precison_at_n = array();


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

  $sql_query = 'SELECT * FROM test_1_50_queries';
  $re = query_the_database($sql_query, $con);

  for ($i = 0; $i < 50; $i++) 
  {
    $result[] = mysql_fetch_row($re);
    $test_1_precison[] = $result[$i][2];
    $test_1_average_precison[] = $result[$i][3];
    $test_1_precison_at_n[] = $result[$i][4];
  }

  $result = array();

  /**
   * fill test 2 arrays
   **/

  $sql_query = 'SELECT * FROM test_2_50_queries';
  $re = query_the_database($sql_query, $con);

  for ($i = 0; $i < 50; $i++) 
  {
    $result[] = mysql_fetch_row($re);
    $test_2_precison[] = $result[$i][2];
    $test_2_average_precison[] = $result[$i][3];
    $test_2_precison_at_n[] = $result[$i][4];
  }
 

  $result = array();
  /**
   * fill test 3 arrays
   **/

  $sql_query = 'SELECT * FROM test_3_50_queries';
  $re = query_the_database($sql_query, $con);

  for ($i = 0; $i < 50; $i++) 
  {
    $result[] = mysql_fetch_row($re);
    $test_3_precison[] = $result[$i][2];
    $test_3_average_precison[] = $result[$i][3];
    $test_3_precison_at_n[] = $result[$i][4];
  }


  $result = array();
  /**
   * fill test 4 arrays
   **/

  $sql_query = 'SELECT * FROM test_4_50_queries';
  $re = query_the_database($sql_query, $con);

  for ($i = 0; $i < 50; $i++) 
  {
    $result[] = mysql_fetch_row($re);
    $test_4_precison[] = $result[$i][2];
    $test_4_average_precison[] = $result[$i][3];
    $test_4_precison_at_n[] = $result[$i][4];
  }


  function print_chart($first_line_array, $second_line_array, $y_axis_name, $title_name)
  {
   // content="text/plain; charset=utf-8"
    require_once ('jpgraph/jpgraph.php');
    require_once ('jpgraph/jpgraph_line.php');
     
     
    // Create the graph. These two calls are always required
    $graph = new Graph(1000,300);
    $graph->SetScale('textlin');
     
    // Create the linear plot
    $first_line=new LinePlot($first_line_array);
    $first_line->SetColor('red');


    // Create the linear plot
    $second_line=new LinePlot($second_line_array);
    $second_line->SetColor('blue');


    $graph->yaxis->title->Set($y_axis_name);
    $graph->xaxis->title->Set('Queries');

    $graph->title->Set($title_name);

    // Add the plot to the graph
    $graph->Add($first_line);
    $graph->Add($second_line);
     
    // Display the graph
    $graph->Stroke();
  }
 


  /**
   * A: Precision Comparision with no Stemming and no Stopword Removal    Red Line: Aggregated     Blue Line: Non-Aggregated
   **/
  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='a')
  {
    print_chart($test_1_precison, $test_3_precison, 'Precision', 'A: Precision Comparision with no Stemming and no Stopword Removal    Red Line: Aggregated     Blue Line: Non-Aggregated'); 

  }


  /**
   * B: Average Precision Comparision with no Stemming and no Stopword Removal    Red Line: Aggregated     Blue Line: Non-Aggregated
   **/
  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='b')
  {

    print_chart($test_1_average_precison, $test_3_average_precison, 'Average Precision', 'B: Average Precision Comparision with no Stemming and no Stopword Removal    Red Line: Aggregated     Blue Line: Non-Aggregated'); 

  }



  /**
   * C: Precision At N Comparision with no Stemming and no Stopword Removal     Red Line: Aggregated     Blue Line: Non-Aggregated 
   **/
  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='c')
  {

    print_chart($test_2_precison_at_n, $test_4_precison_at_n, 'Precision at N(10)', 'C: Precision At N Comparision with no Stemming and no Stopword Removal     Red Line: Aggregated     Blue Line: Non-Aggregated'); 

  }

  /**
   * D: Precision Comparision with Stemming and Stopword Removal turned on
   **/

  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='d')
  {
    print_chart($test_2_precison, $test_4_precison, 'Precision', 'D: Precision Comparision with Stemming and Stopword Removal turned on     Red Line: Aggregated     Blue Line: Non-Aggregated'); 
  }

  /**
   * E: Average Precision Comparision with Stemming and Stopword Removal turned on
   **/

  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='e')
  {
    print_chart($test_2_average_precison, $test_4_average_precison, 'Average Precision', 'E: Average Precision Comparision with Stemming and Stopword Removal turned on     Red Line: Aggregated     Blue Line: Non-Aggregated'); 
  }

  /**
   * F: Precision At N Comparision with Stemming and Stopword Removal turned on
   **/

  if (isset($_GET['chart_letter']) && $_GET['chart_letter'] ==='f')
  {
    print_chart($test_2_precison_at_n, $test_4_precison_at_n, 'Precision at N(10)', 'E: Precision At N Comparision with Stemming and Stopword Removal turned on     Red Line: Aggregated     Blue Line: Non-Aggregated'); 
  }


}


?>
