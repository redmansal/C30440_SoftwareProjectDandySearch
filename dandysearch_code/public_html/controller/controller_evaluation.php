<?php
  include("model/model.php");
  include("model/Evaluation_Class.php");
  include("model/All_Test_Queries.php");

  interface iController
  {
    public function __construct();
    public function invoke();
  }

  class Controller implements iController
  {
    public $bing_results;
    public $yahoo_results;
    public $blekko_results;
    public $google_results;
    public $scoreboard;
    public $all_test_queries;
    public $evaluation;

    public function __construct()
    {
      $this->bing_results = new Search_Bing;
      $this->yahoo_results = new Search_Yahoo;
      $this->blekko_results = new Search_Blekko;
      $this->google_results = new Search_Google;
      $this->scoreboard = new Scoreboard;

      $this->all_test_queries = new All_Test_Queries;
      $this->evaluation = new Evaluation;
    
    }

    public function invoke()
    {
      if(!isset($_GET['source_page']))
      {
          include 'view/evaluation_action.php';
      }

      /**
       * Evaluation Sequence
       **/

      /**
       * Non Aggregated with no Stemming and no stopword removal
       **/

      if(isset($_GET['source_page']) && $_GET['source_page'] === 'perform_evaluation' && $_GET['test_no'] === '1')
      {

        $user_query_checked = new User_query_checked;
        //loop through all 50 test queries perfomring evaluation test on them
        for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
        {
          $user_query_checked->input_user_query = $this->all_test_queries->test_query[$i];

          $this->yahoo_results->set_search_results($user_query_checked);
          $user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($user_query_checked);
          $this->blekko_results->set_search_results($user_query_checked);
          $this->google_results->set_search_results($user_query_checked, 100);
          /*print_r($this->google_results);*/
          
          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);

          //assigning current query to the evaluation record
          $this->evaluation->total_evaluation[$i]->query = $this->all_test_queries->test_query[$i];
          
          /*echo "ok";*/
          $this->evaluation->calculate_evaluation($i, $this->scoreboard, $this->google_results);

          //restore all objects to preset settings prior to next loop
          $user_query_checked = new user_query_checked;
          $this->bing_results = new Search_Bing;
          $this->yahoo_results = new Search_Yahoo;
          $this->blekko_results = new Search_Blekko;
          $this->google_results = new Search_Google;
          $this->scoreboard = new Scoreboard;
        }

        $this->evaluation->calc_average_precision_for_all_queries();
        $this->evaluation->calc_MAP_result();
        $this->evaluation->calc_MAP_result_check();
        $this->evaluation->calc_average_precision_at_n();

        //save results to data base
        $this->evaluation->save_results_to_database(10); //false for safety to avoid overriding


        /*echo ('test 1');*/
        /*$this->evaluation->*/
        /*print_r ($this->evaluation);*/

      }

      /**
       * Non Aggregated with Stemming and stopword removal
       **/

      if(isset($_GET['source_page']) && $_GET['source_page'] === 'perform_evaluation' && $_GET['test_no'] === '2')
      {
        $user_query_checked = new User_query_checked;
        //loop through all 50 test queries perfomring evaluation test on them
        for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
        {
          $user_query_checked->input_user_query = $this->all_test_queries->test_query[$i];


          $user_query_checked->remove_stop_words();
          $user_query_checked->stem_string();


          $this->yahoo_results->set_search_results($user_query_checked);
          $user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($user_query_checked);
          $this->blekko_results->set_search_results($user_query_checked);
          $this->google_results->set_search_results($user_query_checked, 100);
          /*print_r($this->google_results);*/
          
          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);

          //assigning current query to the evaluation record
          /*$this->evaluation->total_evaluation[$i]->query = $this->all_test_queries->test_query[$i];*/
          $this->evaluation->total_evaluation[$i]->query = $user_query_checked->input_user_query;
          
          /*echo "ok";*/
          $this->evaluation->calculate_evaluation($i, $this->scoreboard, $this->google_results);

          //restore all objects to preset settings prior to next loop
          $user_query_checked = new user_query_checked;
          $this->bing_results = new Search_Bing;
          $this->yahoo_results = new Search_Yahoo;
          $this->blekko_results = new Search_Blekko;
          $this->google_results = new Search_Google;
          $this->scoreboard = new Scoreboard;
        }

        $this->evaluation->calc_average_precision_for_all_queries();
        $this->evaluation->calc_MAP_result();
        $this->evaluation->calc_MAP_result_check();
        $this->evaluation->calc_average_precision_at_n();

        //save results to data base
        $this->evaluation->save_results_to_database(20); //false for safety to avoid overriding


        /*echo ('test 1');*/
        /*$this->evaluation->*/
        /*print_r ($this->evaluation);*/
      }

      /**
       * Aggregated with no Stemming and no stopword removal
       **/




      if(isset($_GET['source_page']) && $_GET['source_page'] === 'perform_evaluation' && $_GET['test_no'] === '3')
      {

        $user_query_checked = new User_query_checked;
        //loop through all 50 test queries perfomring evaluation test on them
        for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
        {
          $user_query_checked->input_user_query = $this->all_test_queries->test_query[$i];

          $this->yahoo_results->set_search_results($user_query_checked);
          $user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($user_query_checked);
          $this->blekko_results->set_search_results($user_query_checked);
          $this->google_results->set_search_results($user_query_checked, 100);
          /*print_r($this->google_results);*/
          
          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);
          $this->scoreboard->calculate_borda_scores_and_sort($this->bing_results, $this->yahoo_results, $this->blekko_results);
//

          //assigning current query to the evaluation record
          $this->evaluation->total_evaluation[$i]->query = $this->all_test_queries->test_query[$i];
          /*$this->evaluation->total_evaluation[$i]->query =$user_query_checked->input_user_query;*/
          
          $this->evaluation->calculate_evaluation($i, $this->scoreboard, $this->google_results);

          //
          //restore all objects to preset settings prior to next loop
          $user_query_checked = new user_query_checked;
          $this->bing_results = new Search_Bing;
          $this->yahoo_results = new Search_Yahoo;
          $this->blekko_results = new Search_Blekko;
          $this->google_results = new Search_Google;
          $this->scoreboard = new Scoreboard;
        }

        $this->evaluation->calc_average_precision_for_all_queries();
        $this->evaluation->calc_MAP_result();
        $this->evaluation->calc_MAP_result_check();
        $this->evaluation->calc_average_precision_at_n();

        //save results to data base
        $this->evaluation->save_results_to_database(3);


        /*$this->evaluation->*/
      }

      /**
       * Aggregated with Stemming and stopword removal
       **/



      if(isset($_GET['source_page']) && $_GET['source_page'] === 'perform_evaluation' && $_GET['test_no'] === '4')
      {
 
        $user_query_checked = new User_query_checked;
        //loop through all 50 test queries perfomring evaluation test on them
        for ($i = 0; $i < TOTAL_QUERIES_TO_TEST; $i++) 
        {
          $user_query_checked->input_user_query = $this->all_test_queries->test_query[$i];


          $user_query_checked->remove_stop_words();
          $user_query_checked->stem_string();


          $this->yahoo_results->set_search_results($user_query_checked);
          $user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($user_query_checked);
          $this->blekko_results->set_search_results($user_query_checked);
          $this->google_results->set_search_results($user_query_checked, 100);
          /*print_r($this->google_results);*/
          
          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);
          $this->scoreboard->calculate_borda_scores_and_sort($this->bing_results, $this->yahoo_results, $this->blekko_results);


          //assigning current query to the evaluation record
          /*$this->evaluation->total_evaluation[$i]->query = $this->all_test_queries->test_query[$i];*/
          $this->evaluation->total_evaluation[$i]->query = $user_query_checked->input_user_query;
          
          $this->evaluation->calculate_evaluation($i, $this->scoreboard, $this->google_results);

          //restore all objects to preset settings prior to next loop
          $user_query_checked = new user_query_checked;
          $this->bing_results = new Search_Bing;
          $this->yahoo_results = new Search_Yahoo;
          $this->blekko_results = new Search_Blekko;
          $this->google_results = new Search_Google;
          $this->scoreboard = new Scoreboard;
        }

        $this->evaluation->calc_average_precision_for_all_queries();
        $this->evaluation->calc_MAP_result();
        $this->evaluation->calc_MAP_result_check();
        $this->evaluation->calc_average_precision_at_n();

        //save results to data base
        $this->evaluation->save_results_to_database(40);


        /*$this->evaluation->*/     
      }


    }

  }
?>
