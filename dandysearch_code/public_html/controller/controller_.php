<?php
  include("model/model.php");

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
    public $user_query_checked;

    //constructor for all the abstract data types
    public function __construct()
    {
      $this->bing_results = new Search_Bing;
      $this->yahoo_results = new Search_Yahoo;
      $this->blekko_results = new Search_Blekko;
      $this->google_results = new Search_Google;
      $this->user_query_checked = new User_query_checked;
      $this->scoreboard = new Scoreboard;
    }

    public function invoke()
    {
      //show the homepage if the the url is only queried
      if(!isset($_GET['source_page']))
      {
          include 'view/home_search.php';
      }


      /**
       * Links selected
       **/

      //if urn contains source_page = links show certain pages
      if(isset($_GET['source_page']) && $_GET['source_page'] === 'links')
      {

        if($_GET['display'] === 'home')
        {

          include 'view/home_search.php';
        }
        
        
        if($_GET['display'] === 'help')
        {

          include 'view/help.php';
        }
        
        //eg if a uri looks like http://www.dandysearch.com/index.php?source_page=links&display=about  the if below will be triggered
        if($_GET['display'] === 'about')
        {

          include 'view/about.php';
        }
      }

      /**
       * homesearch
       **/

      if(isset($_GET['source_page']) && $_GET['source_page'] === 'home_search')
      {
        if($_GET['user_query'] === '')
        {
          include 'view/home_search.php';
        }

        if(isset($_GET['user_query']) && $_GET['user_query'] != '')
        {

        $this->user_query_checked->input_user_query = $_GET['user_query'];

        //check to see if there is more than 2 terms in the query and if so check if there are any boolean expressions
        //if so change to + - |
        if ((str_word_count($this->user_query_checked->input_user_query, 0)) > 2)
        {
          $this->user_query_checked->change_to_boolean();
        }

        //there is no need to turn the last user_query_checked into a uri string before sending it into the yahoo object because 
        //the yahho boss object is able to handle this already.
        $this->yahoo_results->set_search_results($this->user_query_checked);

        $this->user_query_checked->turn_into_url_text();
        $this->bing_results->set_search_results($this->user_query_checked);
        $this->blekko_results->set_search_results($this->user_query_checked);

        //populating the scoreboard object with search results from bing, yahoo and blekko
        $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);

        //calling this method aggregates the scorebord and sorts them
        $this->scoreboard->calculate_borda_scores_and_sort($this->bing_results, $this->yahoo_results, $this->blekko_results);

        include 'view/top_of_page.php';
        include 'view/print_score_board.php';
        include 'view/bottom_of_page.php';
        }

      }

      /**
       * Advanced search
       **/

      //if the user clicks advanced this if will be triggered
      if(isset($_GET['source_page']) && $_GET['source_page'] === 'advanced')
      {
       include 'view/select_search_engine.php';
      }


      /**
       * Select_Search_Type 
       **/

      //after the user selects what type of display method he/she want this statment will be triggered
      if(isset($_GET['source_page']) && $_GET['source_page'] === 'select_search_type')
      {
        if(isset($_GET['select_option']) && $_GET['select_option'] === 'bing_yahoo_blekko_google')
        {
         include 'view/bing_yahoo_blekko_google_options.php';
        }

        if(isset($_GET['select_option']) && $_GET['select_option'] === 'bing_yahoo_blekko_merged')
        {
         include 'view/bing_yahoo_blekko_google_merged_options.php';
        }

        if(isset($_GET['select_option']) && $_GET['select_option'] === 'aggregated')
        {
         include 'view/aggregated_options.php';
        }
        
        if(!isset($_GET['select_option']))
        {
          include 'view/select_search_engine.php';
        }

      }


      /**
       * OPTION 1  Show Results for Bing, Yahoo, Blekko and Google search
       **/

      //if the user selects Show "Search Results for Bing, Yahoo, Blekko and Google in separate lists
      if(isset($_GET['source_page']) && $_GET['source_page'] === 'bing_yahoo_blekko_google_options')
      {
        //check to see if the user enter a blank query and if so show the user the page again
        if($_GET['user_query'] === '')
        {
         include 'view/bing_yahoo_blekko_google_options.php';
        }

        if(isset($_GET['user_query']) && $_GET['user_query'] != '')
        {

          $this->user_query_checked->input_user_query = $_GET['user_query'];

          //these ifs take into account the user selectectins such as stemming etc.
          /*var_dump($user_query_checked);*/
          if(isset($_GET['stopword_on']))
          {
            $this->user_query_checked->remove_stop_words();
          }
          /*var_dump($user_query_checked);*/

          if(isset($_GET['stemming_on']))
          {
           $this->user_query_checked->stem_string();
          }

          if(isset($_GET['agg_with_clust']))
          {
           ;//call all functions to perform clusteirng
          }

          if(isset($_GET['remove_puncuation_on']))
          {
           $this->user_query_checked->remove_punctuation();
          }

          //check to see if there is more than 2 terms in the query and if so check if there are any boolean expressions
          //if so change to + - |
          if ((str_word_count($this->user_query_checked->input_user_query, 0)) > 2)
          {
           $this->user_query_checked->change_to_boolean();
          }
          //call method to make url correct for each search type

          
          $this->yahoo_results->set_search_results($this->user_query_checked);
          $this->user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($this->user_query_checked);
          $this->blekko_results->set_search_results($this->user_query_checked);
          $this->google_results->set_search_results($this->user_query_checked,TOTAL_SEARCH_NO);

          include 'view/top_of_page.php';
          include 'view/print_bing_results.php';
          include 'view/print_yahoo_results.php';
          include 'view/print_blekko_results.php';
          include 'view/print_google_results.php';
          include 'view/bottom_of_page.php';
        }
      }

      /**
       * OPTION 2  Show a non aggregated list with Bing, Yahoo, Blekko, and Google Serches merched
       **/

      if(isset($_GET['source_page']) && $_GET['source_page'] === 'bing_yahoo_blekko_google_merged_options')
      {
        //check to see if the user enter a blank query and if so show the user the page again
        if($_GET['user_query'] === '')
        {
         include 'view/bing_yahoo_blekko_google_merged_options.php';
        }

        if(isset($_GET['user_query']) && $_GET['user_query'] != '')
        {
          $this->user_query_checked->input_user_query = $_GET['user_query'];

           /*var_dump($user_query_checked);*/
          if(isset($_GET['stopword_on']))
          {
            $this->user_query_checked->remove_stop_words();
          }
           /*var_dump($user_query_checked);*/

          if(isset($_GET['stemming_on']))
          {
           $this->user_query_checked->stem_string();
          }

          if(isset($_GET['agg_with_clust']))
          {
           ;//call all functions to perform clusteirng
          }

          if(isset($_GET['remove_puncuation_on']))
          {
            $this->user_query_checked->remove_punctuation();
          }

          /*var_dump($user_query_checked);*/
          //check to see if there is more than 2 terms in the query and if so check if there are any boolean expressions
          //if so change to + - |
          if ((str_word_count($this->user_query_checked->input_user_query, 0)) > 2)
          {
           $this->user_query_checked->change_to_boolean();
          }

          /*var_dump($user_query_checked);*/

          $this->yahoo_results->set_search_results($this->user_query_checked);
          $this->user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($this->user_query_checked);
          $this->blekko_results->set_search_results($this->user_query_checked);
          /*$this->google_results->set_search_results($user_query_checked);*/

          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);

          include 'view/top_of_page.php';
          include 'view/print_score_board.php';
          include 'view/bottom_of_page.php';
        }
      }

      /**
       * OPTION 3 Show aggregated results using the borda method
       **/

      if(isset($_GET['source_page']) && $_GET['source_page'] === 'aggregated_options')
      {
        //check to see if the user enter a blank query and if so show the user the page again
        if($_GET['user_query'] === '')
        {
         include 'view/aggregated_options.php';
        }

        if(isset($_GET['user_query']) && $_GET['user_query'] != '')
        {
          $this->user_query_checked->input_user_query = $_GET['user_query'];

          if(isset($_GET['stopword_on']))
          {
            $this->user_query_checked->remove_stop_words();
          }

          if(isset($_GET['stemming_on']))
          {
            $this->user_query_checked->stem_string();
          }

          if(isset($_GET['agg_with_clust']))
          {
            ;//call all functions to perform clusteirng
          }

          if(isset($_GET['remove_puncuation_on']))
          {
            $this->user_query_checked->remove_punctuation();
          }
          //check to see if there is more than 2 terms in the query and if so check if there are any boolean expressions
          //if so change to + - |
          if ((str_word_count($this->user_query_checked->input_user_query, 0)) > 2)
          {
            $this->user_query_checked->change_to_boolean();
          }

          $this->yahoo_results->set_search_results($this->user_query_checked);
          $this->user_query_checked->turn_into_url_text();
          $this->bing_results->set_search_results($this->user_query_checked);
          $this->blekko_results->set_search_results($this->user_query_checked);

          $this->scoreboard->fill_in_scroreboard_details($this->bing_results, $this->yahoo_results, $this->blekko_results);
          $this->scoreboard->calculate_borda_scores_and_sort($this->bing_results, $this->yahoo_results, $this->blekko_results);

          include 'view/top_of_page.php';
          include 'view/print_score_board.php';
          include 'view/bottom_of_page.php';
        }
      }
    }
  }
?>
