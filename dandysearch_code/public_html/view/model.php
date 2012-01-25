<?php
//set the server responce time limit to zero
set_time_limit(0);


define('TOTAL_SEARCH_NO', '50');

/**
 * CLASS DEFINITIONS
 **/


class Search_details
{
  private $search_details_array = array();

  function __construct()
  {
    $this->search_details_array["title"] = 'text';
    $this->search_details_array["description"] = 'text';
    $this->search_details_array["url"] = 'text';
    $this->search_details_array["display_url"] = 'text';
  }

  public function __set($key, $value)
  {
    $this->search_details_array[$key] = $value;
  }

  public function __get($key)
  {
    return $this->search_details_array[$key];
  }

}

class Search_Details_Combined_Results extends Search_details
{
  private $title_tokens_array = array();
  private $description_tokens_array = array();
  
  function __construct()
  {
    /*$this->title = 'text';*/
    /*$this->description = 'text';*/
    /*$this->url = 'text';*/
    /*$this->display_url = 'text';*/

    $this->title_tokens_array[0] = 'zero';
    $this->title_tokens_array[1] = 'one';
    $this->description_tokens_array[0] = 'zeor';
    $this->description_tokens_array[1] = 'one';
  }

  function set_title_tokens_array_element($index, $input)
  {
    $this->title_tokens_array[$index] = $input;
  }

  function set_description_tokens_array_element($index, $input)
  {
    $this->title_tokens_array[$index] = $input;
  }

  function get_title_tokens_array_element($index)
  {
    return $this->title_tokens_array[$index];
  }

  function get_description_tokens_array_element($index)
  {
    return $this->description_tokens_array[$index];
  }

}

include 'User_query_checked_Class.php';


class Search 
{
  public $array_of_results = array();
  public $search_result;

  function __construct()
  {
    for ($i = 0; $i < TOTAL_SEARCH_NO; $i++) 
    {
      $this->array_of_results[$i] = new Search_details;
    }
  }

  function get_array_of_results($z)
  {
    return $this->array_of_results[$z];
  }

  function get_search_result()
  {
    return $this->array_of_results;
  }

/*
 *  function set_search_result_aggregate($bing_results, $yahoo_results, $yobal_results)
 *  {
 *
 *      $this->search_result = $bing_results->get_search_result();
 *  }
 */
    
}

class Search_Bing extends Search
{
  function set_search_results($search_entry)
  {
    /*echo "test1";*/
    /*var_dump ($search_entry->array_of_query_types["input_user_query"]);*/
    $request = 'http://api.bing.net/xml.aspx?AppId=753288BB83E89E80D75157ACE521DB923766F77B&Verstion=2.2&Market=en-US&Query='.$search_entry->input_user_query.'&Sources=web+spell&web.count='.TOTAL_SEARCH_NO.'&xmltype=elementbased';
    /*echo "test2";*/
    //NB use urlencode($query) function put in later

    //echo($request);
    $this->search_result= file_get_contents($request);
    $xml_object = new SimpleXmlElement($this->search_result);

    $children =  $xml_object->children('http://schemas.microsoft.com/LiveSearch/2008/04/XML/web');

    /*print_r($children);*/
    
    $counter = 0;
    foreach ($children->Web->Results->WebResult as $node) 
    {
      $this->array_of_results[$counter]->title = $node->Title; 
      $this->array_of_results[$counter]->description = $node->Description; 
      $this->array_of_results[$counter]->url = $node->Url; 
      $this->array_of_results[$counter]->display_url = $node->DisplayUrl; 
      $counter++;
    }

    /*$namespaces = $node->getNamespaces(true);*/
    /*var_dump($namespaces);*/

  }
}

class Search_Yahoo extends Search
{
  function set_search_results($search_entry)
  {

/*select * from search.web(50) where appid = "dj0yJmk9SlhmUUg0d2xZeElpJmQ9WVdrOWNtaERNM0pUTnpJbWNHbzlNVGM0TXpreE1ESTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1lMQ--" and query = "tinahely" */

    $request = 'http://query.yahooapis.com/v1/public/yql?q=select%20title%2Cabstract%2C%20url%2C%20dispurl%20from%20search.web('.TOTAL_SEARCH_NO.')%20where%20appid%20%3D%20%22dj0yJmk9SlhmUUg0d2xZeElpJmQ9WVdrOWNtaERNM0pUTnpJbWNHbzlNVGM0TXpreE1ESTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1lMQ--%22%20and%20query%20%3D%20%22'.$search_entry->input_user_query.'%22&diagnostics=true';

    //NB use urlencode($query) function put in later

    /*
     *$session = curl_init($request); 
     *curl_setopt($session, CURLOPT_RETURNTRANSFER,true);     
     *$xml = curl_exec($session);
     */
    $this->search_result = file_get_contents($request);

    $xml_object = new SimpleXmlElement($this->search_result);
    /*Print_R($xml_object);*/

    $counter = 0;
    foreach ($xml_object->results->result as $node) 
    {
      $this->array_of_results[$counter]->description = $node->abstract; 
      $this->array_of_results[$counter]->display_url = $node->dispurl; 
      $this->array_of_results[$counter]->title = $node->title; 
      $this->array_of_results[$counter]->url = $node->url; 
      $counter++;
    }
  }

}

class Search_Blekko extends Search
{
  function set_search_results($search_entry)
  {
      $blekko_total_search_no = TOTAL_SEARCH_NO + 10;
      // Get the json from blekko cURL
      $ch = curl_init();
      /*echo 'blekko test';*/
      /*var_dump($search_entry->input_user_query);*/
      curl_setopt($ch, CURLOPT_URL, 'http://blekko.com/ws/?q='.$search_entry->input_user_query.'/json+/ps='.$blekko_total_search_no.'&auth=3Cb58f6ba2&p=0');

      /*curl_setopt($ch, CURLOPT_USERAGENT,*/
      /*  "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2b2) Gecko/20091108 Firefox/3.6b2");*/
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $ret = curl_exec($ch);
      curl_close($ch);
      if ($ret === false) {
        echo "Could not fetch Blekko page";
        exit;
      }
      /*var_dump($ret);*/

      $blekko_object = json_decode($ret);
      
      /*var_dump($blekko_object);*/

      /*echo ($blekko_object->RESULT[1]->snippet);*/
      /*foreach ($*/

      /*var_dump($blekko_object);*/
      $blekko_object_counter = 0;

      if ($blekko_object != null)
      {

      for ($i = 0; $i < TOTAL_SEARCH_NO && isset($blekko_object->RESULT[$blekko_object_counter]); $i++) 
      {
          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet))
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet)) 
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet))
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet)) 
          {
            $blekko_object_counter++; //
          }

          $this->array_of_results[$i]->description = $blekko_object->RESULT[$blekko_object_counter]->snippet; 
          $this->array_of_results[$i]->title = $blekko_object->RESULT[$blekko_object_counter]->url_title;
          $this->array_of_results[$i]->url = $blekko_object->RESULT[$blekko_object_counter]->url; 
          $this->array_of_results[$i]->display_url = $blekko_object->RESULT[$blekko_object_counter]->display_url; 
          $blekko_object_counter++;
      }
      }
    
  }
}

class Search_Google extends Search 
{

  function set_search_results($search_entry, $no_of_search_results_wanted)
  {
    //for the evaluation 100 results is needed but for the user search engine 50 is only needed
    $condition_no = 0;
    if($no_of_search_results_wanted === 100)
    {
      $condition_no = 92;
    }
    else
    {
      $condition_no = 42;
    }

    $counter = 0;
    $counter_json = 0;
    $last_index_used = 0;
    for ($j = 1; $j < $condition_no; $j = $j+10) 
    {
       // code...
      // create curl resource 
      $ch = curl_init('https://www.googleapis.com/customsearch/v1?key=AIzaSyB57o6TXUPsQEFO5dIjQgqyg45Gjqj6hBo&cx=000549439736960725936:ix-sdf_t3ly&q='.$search_entry->input_user_query.'&start='. $j); 

      // set url 
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

      //return the transfer as a string 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

      // $output contains the output string 
      $output = curl_exec($ch); 

      /*echo ($output);*/
      // close curl resource to free up system resources 
      curl_close($ch);      
      

      $google_object = json_decode($output);
      /*var_dump($google_object);*/

      $counter_json = 0;

      for ($i = $counter; $i < $last_index_used+10; $i++) 
      {
        $this->array_of_results[$i]->title = $google_object->items[$counter_json]->title;
        $this->array_of_results[$i]->description = $google_object->items[$counter_json]->snippet; 
        $this->array_of_results[$i]->url = $google_object->items[$counter_json]->link; 
        $this->array_of_results[$i]->display_url = $google_object->items[$counter_json]->displayLink; 
        $counter_json++;
        $counter++;
      }
      $last_index_used = $counter;
    }

  }
}

class Search_Combined extends Search
{
  function set_search_results($bing_results, $yahoo_results, $yobal_results)
  {
    ;
  }

  function __construct()
  {
    for ($i = 0; $i < TOTAL_SEARCH_NO; $i++) 
    {
      $this->array_of_results[$i] = new Search_Details_Combined_Results();
    }
  }

  function tokenize_title_and_body()
  {
    ;
  }

  function stopword_remove_title_and_desciption_array()
  {
    ;
  }

  function stem_title_description_array()
  {
    ;
  }
}



class Inverted_Index
{
  private $vocabulary = array();

  function __construct()
  {
    $this->vocabulary[0] = array();
    $this->vocabulary[0][0] = 'ssss';
    $this->vocabulary[0][1] = 44;

    $this->vocabulary[0] = array();
    $this->vocabulary[0][0] = 'fffff';
    $this->vocabulary[0][1] = 40;
  }

  function set_Inverted_Index($bing_yahoo_yebol_google_merged_options, $User_query_checked)
  {
    ;
  }
}

include 'model/Scoreboard_Class.php';


include("model/stemming_class.php");
?>
