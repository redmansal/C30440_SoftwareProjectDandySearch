<?php

  class ScoreDetails 
  {
    private $score_details_array = array();

    function __construct()
    {
      //index recorded to keep track of its ranking
      $this->score_details_array["bing_index"] = -1;
      $this->score_details_array["yahoo_index"] = -1;
      $this->score_details_array["blekko_index"] = -1;
      //the snippets unique url will be used to identify each snippet uniquely
      $this->score_details_array["unique_url"] = 'www.fake.com';
      $this->score_details_array["title"] = 'fake_title';
      $this->score_details_array["description"] = 'fake_description';
      $this->score_details_array["score"] = 0;
    }

    public function __set($key, $value)
    {
      $this->score_details_array[$key] = $value;
    }

    public function __get($key)
    {
      return $this->score_details_array[$key];
    }

  }

  class Scoreboard 
  {
    public $borda_data_list = array();
    function __construct()
    {

      $score_board_size = TOTAL_SEARCH_NO * 3;
      for ($i = 0; $i < $score_board_size; $i++) 
      {
        $this->borda_data_list[$i] = new ScoreDetails;
      }
      /*$this->borda_data_list['total_no_elements'] = 0;*/

    }

    public function __set($key, $value)
    {
      $this->borda_data_list[$key] = $value;
    }

    public function __get($key)
    {
      return $this->borda_data_list[$key];
    }

    public function need_to_add_this_url($url_string, $engine_no, $ranking_element)
    {
      $boolean_to_return = true;
      $score_board_size = TOTAL_SEARCH_NO * 3;

      //loop will continue when $i < 150 and url is not equal to www.fake.com and when havent found a duplicate url
      for ($i = 0; $i < $score_board_size && $url_string != 'www.fake.com' && $boolean_to_return == true ; $i++) 
      {
        if (strcmp($this->borda_data_list[$i]->unique_url, $url_string) === 0)
        {
          $boolean_to_return = false;

          //if the same url occurs in multiple engines we need to keep a track of it and what engines it occured in
          if ($engine_no === 1)
            $this->borda_data_list[$i]->bing_index = $ranking_element;

          if ($engine_no === 2)
            $this->borda_data_list[$i]->yahoo_index = $ranking_element;

          if ($engine_no ===3)
            $this->borda_data_list[$i]->blekko_index = $ranking_element;
        }
        else
        {
          $boolean_to_return = true;
        }
      }

      return $boolean_to_return;

    }

    function fill_in_scroreboard_details($bing_results, $yahoo_results, $blekko_results)
    {
       /**
        * populating the score board with unique URLS,index locations,title and description from the three searches
        **/

      $score_board_size = TOTAL_SEARCH_NO * 3;

      $score_board_current_index = 0;

      for ($i = 0; $i < TOTAL_SEARCH_NO; $i++) 
      {
        if($this->need_to_add_this_url($bing_results->array_of_results[$i]->url, 1, $i) === true)
        {
          $this->borda_data_list[$score_board_current_index]->bing_index = $i;
          $this->borda_data_list[$score_board_current_index]->unique_url = $bing_results->array_of_results[$i]->url; 
          $this->borda_data_list[$score_board_current_index]->title = $bing_results->array_of_results[$i]->title;
          $this->borda_data_list[$score_board_current_index]->description = $bing_results->array_of_results[$i]->description;
          $score_board_current_index++;
        }

        if($this->need_to_add_this_url($yahoo_results->array_of_results[$i]->url, 2, $i) === true)
        {
          $this->borda_data_list[$score_board_current_index]->yahoo_index = $i;
          $this->borda_data_list[$score_board_current_index]->unique_url = $yahoo_results->array_of_results[$i]->url; 
          $this->borda_data_list[$score_board_current_index]->title = $yahoo_results->array_of_results[$i]->title;
          $this->borda_data_list[$score_board_current_index]->description = $yahoo_results->array_of_results[$i]->description;
          $score_board_current_index++;
        }

        if($this->need_to_add_this_url($blekko_results->array_of_results[$i]->url, 3, $i) === true)
        {
          $this->borda_data_list[$score_board_current_index]->blekko_index = $i;
          $this->borda_data_list[$score_board_current_index]->unique_url = $blekko_results->array_of_results[$i]->url; 
          $this->borda_data_list[$score_board_current_index]->title = $blekko_results->array_of_results[$i]->title;
          $this->borda_data_list[$score_board_current_index]->description = $blekko_results->array_of_results[$i]->description;
          $score_board_current_index++;
        }
      }
      
    }

    function calculate_borda_scores_and_sort()
    {
      /**
       * calulate scores based on current ranking
       **/
      //based on the borda search the highest ranking ranking snippet will be given a score of 50
      //that is say a snippet has a bing index of 0 it will get a score of 50
      //eg  bing_index = 34
      //    yahoo_index = 19
      //    blekko_index = -1(-1 indicates that the snippet did not occur in this search so should get a score of 0)
      //    therefore the resulting borda score will be  (50-34) + (50-19) + (50-50) = 16 + 31 + 0 = 47


      $score_1 = TOTAL_SEARCH_NO;
      $score_2 = TOTAL_SEARCH_NO;
      $score_3 = TOTAL_SEARCH_NO;
      //we initially assume this snippet will get a score of 0

      foreach ($this->borda_data_list as $key) 
      {
        if($key->bing_index > -1)
          $score_1 = $key->bing_index;
        //if it is found that there is an occurance of the snippet in the bing engine its index will be assigned to the score

        if($key->yahoo_index > -1)
          $score_2 = $key->yahoo_index;
        //if it is found that there is an occurance of the snippet in the yahoo engine its index will be assigned to the score

        if($key->blekko_index > -1)
          $score_3 = $key->blekko_index;
        //if it is found that there is an occurance of the snippet in the blekko engine its index will be assigned to the score
        

        $key->score = (TOTAL_SEARCH_NO - $score_1) + (TOTAL_SEARCH_NO - $score_2) + (TOTAL_SEARCH_NO - $score_3);
        //the above equation calculates the borda score
        /*echo ($key->score.'<br>  </br>');*/
        $score_1 = TOTAL_SEARCH_NO;
        $score_2 = TOTAL_SEARCH_NO;
        $score_3 = TOTAL_SEARCH_NO;
     // code...
      }


      /**
       * get an array of all the scores and sort
       **/

      $number_list = array(); 
      $index = 0;
      foreach($this->borda_data_list as $node)
      {
        $number_list[$index] = $node->score;
        $index++;
      }
      rsort($number_list);
      /*var_dump($number_list);*/
      
      /**
       * loop through the sorted list and assign matching borda data elements to the a temporay borda object
       **/

      $temp_scoreboard = new Scoreboard();
      $temp_counter = 0;

      $already_assigned = -1;
      $score_board_size = TOTAL_SEARCH_NO * 3;

      foreach ($number_list as $element) //loop through the sorted list 
      {
        /*print_r($temp_scoreboard);*/
        for ($i = 0; $i < $score_board_size; $i++) //loop through the main scoreboard object taking out results 
        {
          if($element === $this->borda_data_list[$i]->score && $i != $already_assigned)
            //because some snippets will get the same score as others, this needs to be differenciated
            //if a snippet has already been assigned to the temp object from the current object
            //the && != $already_assigned ensures this will not be assigned again
         {
           $temp_scoreboard->borda_data_list[$temp_counter]->bing_index = $this->borda_data_list[$i]->bing_index;
           $temp_scoreboard->borda_data_list[$temp_counter]->yahoo_index = $this->borda_data_list[$i]->yahoo_index;
           $temp_scoreboard->borda_data_list[$temp_counter]->blekko_index = $this->borda_data_list[$i]->blekko_index;
           $temp_scoreboard->borda_data_list[$temp_counter]->unique_url = $this->borda_data_list[$i]->unique_url;
           $temp_scoreboard->borda_data_list[$temp_counter]->score = $this->borda_data_list[$i]->score;
           $temp_scoreboard->borda_data_list[$temp_counter]->title = $this->borda_data_list[$i]->title;
           $temp_scoreboard->borda_data_list[$temp_counter]->description = $this->borda_data_list[$i]->description;


           $already_assigned = $i;
           break;
         }
        }
        $temp_counter++;
      }

      /**
       * copy info from the temp object into the main scoreboard
       **/
      $i = 0;

      foreach($this->borda_data_list as $node)  //copying data from the temp to main borda score board
      {
        $node->bing_index = $temp_scoreboard->borda_data_list[$i]->bing_index;
        $node->yahoo_index = $temp_scoreboard->borda_data_list[$i]->yahoo_index;
        $node->blekko_index = $temp_scoreboard->borda_data_list[$i]->blekko_index;
        $node->unique_url = $temp_scoreboard->borda_data_list[$i]->unique_url;
        $node->title = $temp_scoreboard->borda_data_list[$i]->title;
        $node->description = $temp_scoreboard->borda_data_list[$i]->description;
        $node->score = $temp_scoreboard->borda_data_list[$i]->score;
        $i++;
      }
      
    }

  }

?>


