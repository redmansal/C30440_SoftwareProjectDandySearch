<?php


/**
 * All the calculations are based on the following formulas
 **/
/*
 *Calculation on Each query
 *Precision = no snippets appearing in both the aggregated list and google search / total snippets in aggregated list. 
 *Average Precision = sum of precisions at each recall point /  total snippets in aggregated list.
 *Precision at n(10) =  no snippets appearing in both the aggregated list and google search in the top 10 / 10.
 *
 *Calculation on the average of all 50 queries
 *Mean Average Precision = sum of Average Precisions for the 50 queries/ 50.
 *Average Precision for All queries = Sum of 50 precisions / 50.
 *Average Precision at n = sum of 50 Precisions ant n(10) / 50.
 *
 */

define('TOTAL_QUERIES_TO_TEST', '1');  //NB In the final evaluation that was run this figure was 50 I changed it to 1 here stop google and
                                       //and yahoo being queried to much which could cost me money!!!

class Relevance_Record_Location
{
  public $relevance_record_location_array = array();

  function __construct()
  {
    //this array will assign all the snippets to be not present initially with -1
    for ($i = 0; $i < 150; $i++) 
    {
      $this->relevance_record_location_array[$i] = -1;
    }
    /*var_dump($this->relevance_record_location_array);*/
  }

  public function __set($key, $value)
  {
    $this->relevance_record_location_array[$key] = $value;
  }

  public function __get($key)
  {
    return $this->relevance_record_location_array[$key];
  }
}

class Evaluation_detail
{
  private $evaluation_details = array();

  function __construct()
  {
    $this->evaluation_details["query"] = 'text';
    $this->evaluation_details["precision"] = -1.0;
    $this->evaluation_details["average_precison"] = -1.0;
    $this->evaluation_details["precision_at_n"] = -1.0;
    $this->evaluation_details["relevance_record_location"] = new Relevance_Record_Location;
  }

  public function __set($key, $value)
  {
    $this->evaluation_details[$key] = $value;
  }

  public function __get($key)
  {
    return $this->evaluation_details[$key];
  }
}

class Evaluation
{
  public $total_evaluation = array();

  function __construct()
  {
    for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
    {
      $this->total_evaluation[$i] = new Evaluation_detail;
    }
    $this->total_evaluation['average_precision_for_all_queries'] = 0.00;
    $this->total_evaluation['MAP_result'] = 0.00;
    $this->total_evaluation['MAP_result_check'] = 0.00;
    $this->total_evaluation['average_precision_at_n'] = 0.00;
    $this->total_evaluation['no_of_retreived_docs'] = 0;
    $this->total_evaluation['relevant'] = 0;
    $this->total_evaluation['no_relevant_in_top_ten'] = 0;
  }

  public function __set($key, $value)
  {
    $this->total_evaluation[$key] = $value;
  }

  public function __get($key)
  {
    return $this->total_evaluation[$key];
  }


  public function calculate_evaluation($query_index, &$scoreboard, &$google_results)
  {
    //variables for precision calc
    $no_of_relavent_docs = 0;
    $no_of_retreived_docs = 0;


    //varibles for average_precision calc
    $precision_array_at_recall_point = array();
    $total_relevant_recorder = 0;
    $current_ranking = 0.0;

    //variables for calculating precision to the n
    $no_of_relevant_docs_to_n = 0;
    /*print_r ($this);*/

    //loop through the aggregated list
    for ($j = 0; $j < 150 && $scoreboard->borda_data_list[$j]->unique_url != 'www.fake.com'; $j++) 
    {
      //we are here recording the fact that this snippet is not relevant 
      $this->total_evaluation[$query_index]->relevance_record_location->relevance_record_location_array[$j] = 0;
      
      //loop through the google search results
      for ($i = 0; $i < 100; $i++) 
      {
        if (strcmp($google_results->array_of_results[$i]->url, $scoreboard->borda_data_list[$j]->unique_url) === 0)
        {
          //we are here recording the fact that this snippet is relevant
          $this->total_evaluation[$query_index]->relevance_record_location->relevance_record_location_array[$j] = 1;
          /*echo $google_results->array_of_results[$i]->url; */
          //varibles for precision calc
          $no_of_relavent_docs++;

          //varibles for average_precision calc
          $current_ranking = $j + 1;
          $total_relevant_recorder++;
          $precision_array_at_recall_point[] = $total_relevant_recorder / $current_ranking;

          //varibles for precision calc to the n
          if($j < 10)
          {
              
            $no_of_relevant_docs_to_n++;
          }
        }
      }
      $no_of_retreived_docs++;
    }
    echo $no_of_retreived_docs;

    //precision calculation
    $this->total_evaluation[$query_index]->precision = $no_of_relavent_docs / $no_of_retreived_docs; 
    $this->total_evaluation[$query_index]->no_of_retreived_docs =$no_of_retreived_docs; 
    $this->total_evaluation[$query_index]->relevant = $no_of_relavent_docs; 
    

    //average_precision calculation
    $sum_of_precisions = array_sum($precision_array_at_recall_point);
    $this->total_evaluation[$query_index]->average_precison = $sum_of_precisions/$total_relevant_recorder; 


    //precision calc to the n
    $this->total_evaluation[$query_index]->precision_at_n = $no_of_relevant_docs_to_n / 10;
    $this->total_evaluation[$query_index]->no_relevant_in_top_ten = $no_of_relevant_docs_to_n;

    /*print_r ($this);*/
  }

  public function calc_average_precision_for_all_queries()
  {
    /*echo ('calc_average_precision_for_all_queries method called');*/
    $total_precision = 0;
    for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
    {
      $total_precision = $total_precision + $this->total_evaluation[$i]->precision;
    }
    $this->average_precision_for_all_queries = $total_precision / TOTAL_QUERIES_TO_TEST;

  }

  
  public function calc_MAP_result()
  {
    $total_precision = 0;
    for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
    {
      $total_precision = $total_precision + $this->total_evaluation[$i]->average_precison;

    }
    $this->MAP_result = $total_precision / TOTAL_QUERIES_TO_TEST;
    /*;*/
  }

  //this second MAP calc was run to check the validy of the first set of results, It calculated on the relevance_record_location_array
  public function calc_MAP_result_check()
  {
    $relevance_counter = 0;
    $sum_of_all_elements_in_array = 0;
    $all_average_precisions = 0;

    for ($j = 0; $j < TOTAL_QUERIES_TO_TEST; $j++) 
    {
      $precision_at_recall_point = array();
      for ($i = 0; $i < 150 && $i != -1; $i++) 
      {
          if($this->total_evaluation[$j]->relevance_record_location->relevance_record_location_array[$i] === 1)
          {
            $relevance_counter++;
            $current_ranking = $i + 1;
            $precision_at_recall_point[] = $relevance_counter/$current_ranking;
          }
      }

      $sum_of_all_elements_in_array = array_sum($precision_at_recall_point);
      $current_query_average_precision = $sum_of_all_elements_in_array / $relevance_counter;

      $all_average_precisions = $all_average_precisions + $current_query_average_precision;

      $relevance_counter = 0;
      unset($precision_at_recall_point);
      $sum_of_all_elements_in_array = 0;
    }
    $this->MAP_result_check = $all_average_precisions / TOTAL_QUERIES_TO_TEST;

  }

  public function calc_average_precision_at_n()
  {
    $total_precision = 0;
    for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
    {
      $total_precision = $total_precision + $this->total_evaluation[$i]->precision_at_n;
    }

    $this->average_precision_at_n = $total_precision / TOTAL_QUERIES_TO_TEST;

    /*echo ('calc_average_precision_at_n method called');*/
    /*;*/
  }

  public function query_the_database($sql_query, $con)
  {
      $re = mysql_query($sql_query);

      if (!$re) {
        echo 'Could not run query: ' . mysql_error();
        exit;
      }
  }

  public function save_results_to_database($test_no)
  {
    /**
     * set up connection with the database
     **/

    /*$con = mysql_connect("localhost","root","");*/
    $con = mysql_connect("localhost","dandysea","fake password");

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
    
    //delete everything from the table before putting in new information
    $this->query_the_database('TRUNCATE TABLE test_'.$test_no.'_50_queries', $con);
    $this->query_the_database('TRUNCATE TABLE test_'.$test_no.'_relevance_record_location', $con);

    //loop through the 50 queries recording evaluation details
    for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
    {
      $sql_query = 'INSERT INTO test_'.$test_no.'_50_queries VALUES (\''.$i.'\',\''.$this->total_evaluation[$i]->query.'\', \''.$this->total_evaluation[$i]->precision.'\', \''.$this->total_evaluation[$i]->average_precison.'\', \''.$this->total_evaluation[$i]->precision_at_n.'\', \''.$this->total_evaluation[$i]->no_of_retreived_docs.'\', \''.$this->total_evaluation[$i]->relevant.'\', \''.$this->total_evaluation[$i]->no_relevant_in_top_ten.'\')';

      $this->query_the_database($sql_query, $con);



      /**
       * storing the details recorded in the relevance_record_location object to the database
       **/

      $sql_query_2 = '\''.$i.' \', ';

      for ($j = 0; $j < 149; $j++) 
      {
        $sql_query_2 = $sql_query_2. '\''.$this->total_evaluation[$i]->relevance_record_location->relevance_record_location_array[$j].'\', ';
      }

      $sql_query_2 = 'INSERT INTO test_'.$test_no.'_relevance_record_location VALUES ('.$sql_query_2.'\' '.$this->total_evaluation[$i]->relevance_record_location->relevance_record_location_array[149].'\' )';

      $this->query_the_database($sql_query_2, $con);
    }

    //delete everything from the table before putting in new information
    $this->query_the_database('TRUNCATE TABLE test_'.$test_no.'_averages', $con);

    //insert average results info into database
    $sql_query = 'INSERT INTO test_'.$test_no.'_averages VALUES (\''.$this->average_precision_for_all_queries.'\', \''.$this->MAP_result.'\',  \''.$this->MAP_result_check.'\', \''.$this->average_precision_at_n.'\')';

    $this->query_the_database($sql_query, $con);

    /*echo ('save_results_to_database method called <br>  </br>');*/
    mysql_close($con);

  }

}

?>
